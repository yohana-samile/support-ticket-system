<?php
use App\Models\SubTopic;
use App\Models\TertiaryTopic;
use Illuminate\Database\Seeder;

class TertiaryTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tertiaryTopics = [
            'Failed Transactions' => ['Card Declined', 'Insufficient Funds'],
            'Duplicate Charges' => ['Bank Error', 'System Glitch'],
            'Login Failures' => ['Wrong Password', 'Two Factor Failed'],
            'API Errors' => ['Timeout Error', 'Invalid Token'],
            'Password Reset' => ['Reset Link Not Sent', 'Token Expired'],
            'New Dashboard Features' => ['Analytics Widget', 'Dark Mode'],
            'How to Use the System' => ['User Manual', 'Video Tutorials'],
            'Delayed Delivery' => ['High traffic congestion', 'Queueing issues', 'Network latency'],
            'Undelivered SMS' => ['Invalid phone numbers', 'Number not reachable', 'Operator filters'],
            'Blocked Messages' => ['Spam filters', 'Content policy violations', 'Blacklisted sender ID'],
            'Sender ID Issues' => ['Unregistered sender ID', 'Mismatch with approved sender list', 'Region-specific sender restrictions'],
            'Delivery Reports (DLRs) Not Received' => ['Disabled DLR service', 'Incomplete operator feedback', 'API misconfiguration'],
        ];

        foreach ($tertiaryTopics as $subTopicName => $tertiaries) {
            $subTopic = SubTopic::where('name', $subTopicName)->first();

            if ($subTopic) {
                foreach ($tertiaries as $name) {
                    TertiaryTopic::updateOrCreate([
                        'sub_topic_id' => $subTopic->id,
                        'name' => $name
                    ], [
                        'description' => "$name under $subTopicName"
                    ]);
                }
            }
        }
    }
}
