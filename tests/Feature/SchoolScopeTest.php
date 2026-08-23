<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FeatureSetting;
use App\Models\School;
use App\Models\SchoolDomain;
use App\Models\SchoolMembership;
use App\Traits\FeatureTestTrait;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * School ownership is enforced in one place, for reads and for writes.
 */
class SchoolScopeTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * Records that name their own school instead of taking the working one.
     *
     * A membership grants access to a named school. Filling it from the
     * request would hide a mistake, so it stays outside the trait. A feature
     * setting with no school is the platform default, which the trait would
     * quietly turn into a school setting. A web address is read before anybody
     * signs in, when there is no working school at all, and an address that
     * names no campus opens the whole organization: filling it in would turn
     * that into a campus address by accident.
     *
     * @var array<int, class-string<Model>>
     */
    private const NAMES_ITS_OWN_SCHOOL = [
        SchoolMembership::class,
        FeatureSetting::class,
        SchoolDomain::class,
    ];

    public function test_every_school_owned_model_uses_the_school_scope(): void
    {
        $missing = [];

        foreach ($this->modelClasses() as $class) {
            $model = new $class;

            if (!Schema::hasColumn($model->getTable(), 'school_id')) {
                continue;
            }

            if (in_array($class, self::NAMES_ITS_OWN_SCHOOL, true)) {
                continue;
            }

            if (!in_array(InSchool::class, class_uses_recursive($class), true)) {
                $missing[] = $class;
            }
        }

        $this->assertSame(
            [],
            $missing,
            'These models own a school_id but do not use the InSchool trait: '.implode(', ', $missing)
        );
    }

    public function test_a_new_record_takes_the_school_being_worked_in(): void
    {
        $school = $this->workingSchool();

        school_context()->set($school, remember: false);

        $academicYear = AcademicYear::create(['start_year' => 2100, 'stop_year' => 2101]);

        $this->assertSame($school->id, $academicYear->school_id);
    }

    public function test_a_named_school_is_kept(): void
    {
        $other = School::factory()->create();

        school_context()->set($this->workingSchool(), remember: false);

        $academicYear = AcademicYear::create([
            'start_year' => 2100,
            'stop_year' => 2101,
            'school_id' => $other->id,
        ]);

        $this->assertSame($other->id, $academicYear->school_id);
    }

    /**
     * Get every model class in the application.
     *
     * @return array<int, class-string<Model>>
     */
    private function modelClasses(): array
    {
        $classes = [];

        foreach (Finder::create()->files()->name('*.php')->in(app_path('Models')) as $file) {
            /** @var SplFileInfo $file */
            $class = 'App\\Models\\'.Str::before($file->getRelativePathname(), '.php');

            if (!class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract() || !$reflection->isSubclassOf(Model::class)) {
                continue;
            }

            $classes[] = $class;
        }

        return $classes;
    }
}
