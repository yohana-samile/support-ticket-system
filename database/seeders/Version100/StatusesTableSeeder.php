<?php

use App\Models\Status;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $count = Status::count('id');
        if ($count === 0) {
            $statuses = [
                [
                    'slug' => 'open',
                    'name' => 'Open',
                    'color_class' => 'bg-warning',
                    'text_color_class' => 'text-dark',
                ],
                [
                    'slug' => 'in_progress',
                    'name' => 'In Progress',
                    'color_class' => 'bg-primary',
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'on_hold',
                    'name' => 'On Hold',
                    'color_class' => 'bg-secondary',
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'resolved',
                    'name' => 'Resolved',
                    'color_class' => 'bg-success',
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'closed',
                    'name' => 'Closed',
                    'color_class' => 'bg-light',
                    'text_color_class' => 'text-dark',
                ],
                [
                    'slug' => 'escalated',
                    'name' => 'Escalated',
                    'color_class' => 'bg-danger',
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'pending_customer',
                    'name' => 'Pending Customer',
                    'color_class' => 'bg-info',
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'pending_third_party',
                    'name' => 'Pending Third Party',
                    'color_class' => 'bg-indigo', // Bootstrap doesn't have "indigo", you may define this in custom CSS or use 'bg-primary'
                    'text_color_class' => 'text-white',
                ],
                [
                    'slug' => 'reopened',
                    'name' => 'Reopened',
                    'color_class' => 'bg-warning',
                    'text_color_class' => 'text-dark',
                ],
                [
                    'slug' => 'duplicate',
                    'name' => 'Duplicate',
                    'color_class' => 'bg-danger',
                    'text_color_class' => 'text-white',
                ],
            ];

            DB::table('statuses')->insert($statuses);
        }
    }
}
