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
                    'color_class' => 'bg-yellow-100',
                    'text_color_class' => 'text-yellow-800',
                ],
                [
                    'slug' => 'in_progress',
                    'name' => 'In Progress',
                    'color_class' => 'bg-blue-100',
                    'text_color_class' => 'text-blue-800',
                ],
                [
                    'slug' => 'on_hold',
                    'name' => 'On Hold',
                    'color_class' => 'bg-purple-100',
                    'text_color_class' => 'text-purple-800',
                ],
                [
                    'slug' => 'resolved',
                    'name' => 'Resolved',
                    'color_class' => 'bg-green-100',
                    'text_color_class' => 'text-green-800',
                ],
                [
                    'slug' => 'closed',
                    'name' => 'Closed',
                    'color_class' => 'bg-gray-100',
                    'text_color_class' => 'text-gray-800',
                ],
                [
                    'slug' => 'escalated',
                    'name' => 'Escalated',
                    'color_class' => 'bg-red-100',
                    'text_color_class' => 'text-red-800',
                ],
                [
                    'slug' => 'pending_customer',
                    'name' => 'Pending Customer',
                    'color_class' => 'bg-orange-100',
                    'text_color_class' => 'text-orange-800',
                ],
                [
                    'slug' => 'pending_third_party',
                    'name' => 'Pending Third Party',
                    'color_class' => 'bg-indigo-100',
                    'text_color_class' => 'text-indigo-800',
                ],
                [
                    'slug' => 'reopened',
                    'name' => 'Reopened',
                    'color_class' => 'bg-amber-100',
                    'text_color_class' => 'text-amber-800',
                ],
                [
                    'slug' => 'duplicate',
                    'name' => 'Duplicate',
                    'color_class' => 'bg-pink-100',
                    'text_color_class' => 'text-pink-800',
                ],
            ];

            DB::table('statuses')->insert($statuses);
        }
    }
}
