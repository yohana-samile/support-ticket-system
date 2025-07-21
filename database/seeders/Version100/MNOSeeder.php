<?php

use App\Models\Operator;
use Illuminate\Database\Seeder;

class MNOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $channels = [
            ['name' => 'Vodacom Tanzania', 'is_active' => true],
            ['name' => 'Airtel Tanzania', 'is_active' => true],
            ['name' => 'Yas', 'is_active' => true],
            ['name' => 'TTCL', 'is_active' => false],
            ['name' => 'Halotel', 'is_active' => true],
            ['name' => 'All MNO', 'is_active' => true],
        ];

        foreach ($channels as $channel) {
            Operator::updateOrCreate([
                'name' => $channel['name'],
            ], [
                'is_active' => $channel['is_active'],
            ]);
        }
    }
}
