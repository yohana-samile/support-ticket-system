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
                    'slug' => 'reopened',
                    'name' => 'Reopened',
                    'color_class' => 'bg-warning',
                    'text_color_class' => 'text-dark',
                ],
            ];

            DB::table('statuses')->insert($statuses);
        }
    }
}
