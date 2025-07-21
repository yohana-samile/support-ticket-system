<?php

use App\Models\Access\Client;
use App\Models\SenderId;
use App\Models\SubTopic;
use App\Models\TertiaryTopic;
use Illuminate\Database\Seeder;

class SenderIdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $senderIds = [
            ['sender_id' => 'Nextsms'],
            ['sender_id' => 'Mauzo360'],
            ['sender_id' => 'Harusi'],
            ['sender_id' => 'Sherehe'],
            ['sender_id' => 'Harambee'],
            ['sender_id' => 'Kikao'],
        ];

        foreach ($senderIds as $senderId) {
            SenderId::updateOrCreate([
                'sender_id' => $senderId['sender_id']
            ]);
        }

        $clients = Client::all();
        $senderIds = SenderId::all();

        foreach ($clients as $client) {
            $randomSenderIds = $senderIds->random(rand(1, 3));
            $client->senderIds()->syncWithoutDetaching(
                $randomSenderIds->pluck('id')->toArray()
            );
        }
    }
}
