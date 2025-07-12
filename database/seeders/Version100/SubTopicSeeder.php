<?php

use App\Models\SubTopic;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class SubTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $subTopics = [
            'Payment Issue' => ['Failed Transactions', 'Duplicate Charges', 'Refund Requests'],
            'Technical Issue' => ['Login Failures', 'App Crashes', 'API Errors'],
            'Account Problem' => ['Password Reset', 'Email Not Verified', 'Account Locked'],
            'Feature Request' => ['New Dashboard Features', 'Mobile Support', 'Integrations'],
            'General Inquiry' => ['How to Use the System', 'Pricing Info', 'Contact Support'],
            'Sms Delivery Issue' => [
                'Delayed Delivery',
                'Undelivered SMS',
                'Blocked Messages',
                'Sender ID Issues',
                'Delivery Reports (DLRs) Not Received'
            ],
        ];

        foreach ($subTopics as $topicName => $subs) {
            $topic = Topic::where('name', $topicName)->first();

            if ($topic) {
                foreach ($subs as $name) {
                    SubTopic::updateOrCreate([
                        'topic_id' => $topic->id,
                        'name' => $name
                    ], [
                        'description' => "$name related to $topicName"
                    ]);
                }
            }
        }
    }
}
