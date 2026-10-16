<?php

namespace Database\Seeders;

use App\Enums\NoticeRecipientState;
use App\Enums\NoticeStatus;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();
        $admin = User::first();

        if ($school !== null && $admin !== null) {
            $this->seedFor($school, $admin);
        }
    }

    /**
     * Add a small, safe example that demonstrates the notice workflow.
     */
    public function seedFor(School $school, User $admin): void
    {
        $notice = Notice::firstOrCreate(
            ['school_id' => $school->id, 'title' => 'Welcome to Skuul'],
            [
                'content' => 'This sample notice shows where school announcements will appear.',
                'start_date' => now()->toDateString(),
                'stop_date' => now()->addYear()->toDateString(),
                'active' => true,
                'status' => NoticeStatus::Published,
                'audience' => ['user_ids' => [$admin->id]],
                'send_email' => false,
                'published_at' => now(),
                'published_by' => $admin->id,
                'revision' => 1,
            ],
        );

        NoticeRecipient::firstOrCreate(
            ['notice_id' => $notice->id, 'user_id' => $admin->id],
            [
                'state' => NoticeRecipientState::Delivered,
                'delivered_at' => now(),
            ],
        );
    }
}
