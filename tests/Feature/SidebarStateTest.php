<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Models\Organization;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarStateTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * Permissions that render the Students submenu in the sidebar.
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
        $html = $this->authorized_user(self::MENU_PERMISSIONS)
            ->get('dashboard')
            ->assertOk()
            ->getContent();

        foreach ($this->submenuPanels($html) as $panel) {
            $this->assertStringContainsString('x-cloak', $panel, 'A closed submenu must be cloaked.');
        }
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
            ->assertSee('Academic periods')
            ->assertSee('Academic levels')
            ->assertSee('Cycle sections')
            ->assertSee('Course offerings')
            ->assertSee(route('academic-periods.index'), false)
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
        ])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('Gradebooks')
            ->assertSee(route('course-offerings.index'), false)
            ->assertDontSee('Exam records');
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
        preg_match_all('/<div x-show="open" x-collapse[^>]*>/', $html, $matches);

        return $matches[0];
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
