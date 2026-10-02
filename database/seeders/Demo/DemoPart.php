<?php

namespace Database\Seeders\Demo;

/**
 * One area of the demo school, such as teaching or finance.
 */
interface DemoPart
{
    /**
     * Add this area's records to the demo school.
     */
    public function seed(DemoSchool $demo): void;
}
