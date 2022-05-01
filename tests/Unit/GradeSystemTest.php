<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\GradeSystem\GradeSystemService;

class GradeSystemTest extends TestCase
{
    public $gradeSystemService;
   
    //test if grade range exists

    public function test_grade_range_exists()
    {
        $testCases = [
            //check if grade range is in test range
            [
                'grade_from' => 30,
                'grade_to' => 200,
                'expected' => true
            ],
            //check if test range is in grade Range
            [
                'grade_from' => 50,
                'grade_to' => 90,
                'expected' => true
            ],
            //check if test range starts from grade range and stops in another grade range
            [
                'grade_from' => 100,
                'grade_to' => 200,
                'expected' => true
            ],
            //check if grade range starts at grade range and ends outside grade range
            [
                'grade_from' => 88,
                'grade_to' => 150,
                'expected' => true
            ],
            //check if grade range starts outside test range and ends in grade range
            [
                'grade_from' => 150,
                'grade_to' => 200,
                'expected' => true
            ],
            //false when grade range is not in test range
            [
                'grade_from' => 101,
                'grade_to' => 199,
                'expected' => false
            ]
        ];
        $grades = [
            [
                'grade_from' => 40,
                'grade_to' => 100
            ],[
                'grade_from' => 800,
                'grade_to' => 900
            ],
            [
                'grade_from' => 200,
                'grade_to' => 300
            ],
        ];
        $this->gradeSystemService = app(GradeSystemService::class);
        foreach ($testCases as $testCase ) {
            if (!array_key_exists('expected', $testCase)) {
                return dd('expected value not pass in one or more test cases');
            }elseif ($testCase['expected'] == true) {
                $this->assertTrue($this->gradeSystemService->gradeRangeExists($testCase, $grades));
            }elseif ($testCase['expected'] == false) {
                $this->assertFalse($this->gradeSystemService->gradeRangeExists($testCase, $grades));
            }
        }
    }
}
