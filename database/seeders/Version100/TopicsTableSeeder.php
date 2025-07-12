<?php

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $topics = [
            ['name' => 'Payment Issue', 'description' => 'Problems related to payments'],
            ['name' => 'Sms Delivery Issue', 'description' => 'Sms delivery problems with the system'],
            ['name' => 'Technical Issue', 'description' => 'Technical problems with the system'],
            ['name' => 'Account Problem', 'description' => 'Issues with user accounts'],
            ['name' => 'Feature Request', 'description' => 'Requests for new features'],
            ['name' => 'General Inquiry', 'description' => 'General questions'],
        ];

        foreach ($topics as $topic) {
           Topic::updateOrCreate(['name' => $topic['name']], [
               'description' => $topic['description']
            ]);
        }
    }
}
