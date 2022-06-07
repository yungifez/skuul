<?php

namespace Database\Seeders;

use App\Models\Weekday;
use Illuminate\Database\Seeder;

class WeekdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $weekdays = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        foreach ($weekdays as $weekday) {
            Weekday::firstOrCreate(['name' => $weekday]);
        }
    }
}
