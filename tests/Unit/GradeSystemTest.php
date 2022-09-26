<?php

namespace Tests\Unit;

use App\Services\GradeSystem\GradeSystemService;
use PHPUnit\Framework\TestCase;

class GradeSystemTest extends TestCase
{
    public $gradeSystemService;

    //test if grade range exists

    public function test_grade_range_exists()
    {
        $testCases = [
            //check if grade range is in test range
            [
                'grade_from' => 20,
                'grade_till' => 70,
                'expected'   => true,
            ],
            //check if test range is in grade Range
            [
                'grade_from' => 50,
                'grade_till' => 55,
                'expected'   => true,
            ],
            //check if test range starts from grade range and stops in another grade range
            [
                'grade_from' => 50,
                'grade_till' => 80,
                'expected'   => true,
            ],
            //check if grade range starts at grade range and ends outside grade range
            [
                'grade_from' => 88,
                'grade_till' => 150,
                'expected'   => true,
            ],
            //check if grade range starts outside test range and ends in grade range
            [
                'grade_from' => 70,
                'grade_till' => 100,
                'expected'   => true,
            ],
            //false when grade range is not in test range
            [
                'grade_from' => 70,
                'grade_till' => 79,
                'expected'   => false,
            ],
        ];
        $grades = [
            [
                'grade_from' => 40,
                'grade_till' => 60,
            ], [
                'grade_from' => 80,
                'grade_till' => 100,
            ],
            [
                'grade_from' => 20,
                'grade_till' => 40,
            ],
        ];
        $this->gradeSystemService = app(GradeSystemService::class);
        foreach ($testCases as $testCase) {
            if (!array_key_exists('expected', $testCase)) {
                return dd('expected value not pass in one or more test cases');
            } elseif ($testCase['expected'] == true) {
                $this->assertTrue($this->gradeSystemService->gradeRangeExists($testCase, $grades));
            } elseif ($testCase['expected'] == false) {
                $this->assertFalse($this->gradeSystemService->gradeRangeExists($testCase, $grades));
            }
        }
    }
}
