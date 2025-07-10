<?php

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run()
    {
        $channels = [
            ['name' => 'mail', 'icon' => 'fas fa-envelope', 'color' => 'primary'],
            ['name' => 'database', 'icon' => 'fas fa-database', 'color' => 'info'],
            ['name' => 'sms', 'icon' => 'fas fa-sms', 'color' => 'success', 'is_active' => false],
            ['name' => 'whatsapp', 'icon' => 'fab fa-whatsapp', 'color' => 'success', 'is_active' => false]
        ];

        foreach ($channels as $channel) {
            Channel::updateOrCreate(['name' => $channel['name']], [
                'icon' => $channel['icon'],
                'color' => $channel['color'],
            ]);
        }
    }
}
