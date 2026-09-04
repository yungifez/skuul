<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\Feature;
use App\Livewire\Layouts\Menu;
use App\Models\Organization;
use App\Models\SchoolOperatingProfile;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SidebarStateTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * Permissions that render the Students workspace in the sidebar.
     *
     * @var list<string>
     */
    private const MENU_PERMISSIONS = ['read student'];

    public function test_the_sidebar_opens_when_nothing_is_stored(): void
    {
        $this->authorized_user(['read admin'])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="expanded"', false)
            ->assertSee('sidebar(true)', false);
    }

    public function test_the_sidebar_reopens_in_the_state_it_was_left_in(): void
    {
        $this->authorized_user(['read admin'])
            ->withUnencryptedCookie('sidebar_state', 'false')
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="collapsed"', false)
            ->assertSee('data-collapsible="icon"', false)
            ->assertSee('sidebar(false)', false);
    }

    public function test_a_stored_open_sidebar_still_opens(): void
    {
        $this->authorized_user(['read admin'])
            ->withUnencryptedCookie('sidebar_state', 'true')
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="expanded"', false);
    }

    public function test_a_closed_submenu_stays_hidden_until_alpine_starts(): void
    {
        $this->authorized_user(['read boarding']);
        features()->enable(Feature::Boarding);

        $html = $this->get('dashboard')
            ->assertOk()
            ->getContent();

        $panels = $this->submenuPanels($html);

        $this->assertNotEmpty($panels);

        foreach ($panels as $panel) {
            $this->assertStringContainsString('x-cloak', $panel, 'A closed submenu must be cloaked.');
        }
    }

    public function test_multi_page_features_are_grouped_in_the_sidebar(): void
    {
        $this->authorized_user(['read boarding', 'read library']);
        features()->enable(Feature::Boarding);
        features()->enable(Feature::Library);

        /** @var list<array<string, mixed>> $menu */
        $menu = Livewire::test(Menu::class)->get('menu');

        $this->assertSame(
            ['Houses', 'Nights away'],
            $this->submenuLabels($menu, 'Boarding'),
        );
        $this->assertSame(
            ['Catalogue', 'Lending desk', 'Library queue'],
            $this->submenuLabels($menu, 'Library'),
        );
    }

    public function test_the_students_workspace_is_a_direct_link(): void
    {
        $html = $this->authorized_user(self::MENU_PERMISSIONS)
            ->get('dashboard/students')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(route('students.index'), $html);
        $this->assertStringNotContainsString('View students', $this->withoutLivewireSnapshots($html));
    }

    public function test_a_parent_does_not_see_staff_syllabus_or_timetable_navigation(): void
    {
        $this->authorized_user(['read syllabus', 'read timetable']);
        auth()->user()->assignRole('parent');

        $menu = Livewire::test(Menu::class)->get('menu');
        $items = collect($menu)
            ->filter(fn (array $item): bool => $item['visible'] ?? true)
            ->pluck('text')
            ->filter()
            ->all();

        $this->assertNotContains('Syllabi', $items);
        $this->assertNotContains('Timetables', $items);
    }

    public function test_the_sidebar_shows_the_replacement_academic_structure_without_legacy_navigation(): void
    {
        $this->authorized_user([
            'read admin',
            'read academic year',
            'read academic period',
            'read class',
            'read section',
            'read subject',
        ])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('Academic cycles')
            ->assertSee(school_terms('class_level', 'Classes'))
            ->assertSee(school_terms('section', 'Sections'))
            ->assertSee('Course offerings')
            ->assertSee(route('academic-years.index'), false)
            ->assertDontSee('View Classes')
            ->assertDontSee('Class groups')
            ->assertDontSee('Create academic period');
    }

    public function test_the_sidebar_preserves_its_scroll_container_during_navigation(): void
    {
        $html = $this->authorized_user(['read admin'])
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<div(?=[^>]*data-sidebar="content")(?=[^>]*wire:navigate:scroll)[^>]*>/',
            $html,
        );
        $this->assertStringNotContainsString('wire:navigate:scroll', $this->navigationLinks($html));
        $this->assertStringContainsString('wire:navigate', $this->navigationLinks($html));
    }

    public function test_the_sidebar_shows_the_gradebook_workspace(): void
    {
        $this->authorized_user([
            'read admin',
            'menu-gradebook',
            'read gradebook',
            'read subject',
        ])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('Gradebooks')
            ->assertSee(route('course-offerings.index'), false)
            ->assertDontSee('Exam records');
    }

    public function test_the_command_palette_uses_visible_routes_and_school_terms(): void
    {
        $school = $this->workingSchool();
        $school->operatingProfile()->updateOrCreate([], [
            'preset' => SchoolOperatingProfile::DEFAULT_PRESET,
            'labels' => array_replace(SchoolOperatingProfile::labelsFor(SchoolOperatingProfile::DEFAULT_PRESET), [
                'class_level' => 'Grade',
                'section' => 'Homeroom',
            ]),
        ]);

        $this->authorized_user(['read admin', 'read class', 'read section'], $school);

        $items = Livewire::test(Menu::class)->get('commandItems');

        $this->assertIsArray($items);

        $this->assertTrue(collect($items)->contains(fn (array $item): bool => $item['label'] === 'Grades'));
        $this->assertTrue(collect($items)->contains(fn (array $item): bool => $item['label'] === 'Homerooms'));
        $this->assertTrue(collect($items)->contains(fn (array $item): bool => $item['url'] === route('admins.index')));
        $this->assertFalse(collect($items)->contains(fn (array $item): bool => $item['url'] === route('students.index')));
    }

    public function test_the_sidebar_shows_organizations_to_an_organization_administrator(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->actingAs($user)
            ->get(route('organizations.index'))
            ->assertOk()
            ->assertSee('Organizations')
            ->assertSee(route('organizations.index'), false)
            ->assertDontSee(route('organizations.create'), false)
            ->assertDontSee('View Organizations');
    }

    public function test_the_sidebar_hides_an_empty_organization_section(): void
    {
        $html = $this->authorized_user(['read admin'])
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Organization', $this->withoutLivewireSnapshots($html));
    }

    public function test_the_organization_section_uses_the_schools_menu_permission(): void
    {
        $html = $this->authorized_user(['read admin', 'read school'])
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Organization', $this->withoutLivewireSnapshots($html));
        $this->assertStringContainsString(route('schools.index'), $html);
    }

    /**
     * Pull out the opening tag of every collapsible submenu panel.
     *
     * @return list<string>
     */
    private function submenuPanels(string $html): array
    {
        preg_match_all('/<div data-slot="collapsible-content"[^>]*>/', $html, $matches);

        return $matches[0];
    }

    /**
     * Get the labels inside one feature submenu.
     *
     * @param  list<array<string, mixed>>  $menu
     * @return list<string>
     */
    private function submenuLabels(array $menu, string $label): array
    {
        foreach ($menu as $menuItem) {
            if (($menuItem['text'] ?? null) !== $label || !is_array($menuItem['submenu'] ?? null)) {
                continue;
            }

            return array_values(array_map(
                static fn (array $submenu): string => (string) ($submenu['text'] ?? ''),
                $menuItem['submenu'],
            ));
        }

        return [];
    }

    /**
     * Remove component state, which contains menu labels even when Blade hides them.
     */
    private function withoutLivewireSnapshots(string $html): string
    {
        return preg_replace('/wire:snapshot="[^"]*"/', '', $html) ?? $html;
    }

    /**
     * Extract the sidebar links that use Livewire navigation.
     */
    private function navigationLinks(string $html): string
    {
        preg_match_all('/<a[^>]*wire:navigate[^>]*>/', $html, $matches);

        return implode('', $matches[0]);
    }
}
