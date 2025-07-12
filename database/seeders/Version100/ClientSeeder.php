<?php

use App\Models\Access\Client;
use App\Models\SaasApp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $count = Client::query()->count('id');
        if($count == 0) {
            $email = 'client@ticketing.co.tz';
            $password = 12345678;
            $saasAppId = SaasApp::query()->where('abbreviation', 'Nextsms')->value('id');

            Client::updateOrCreate(['email' => $email], [
                "name" => "Client User",
                "is_active" => true,
                "email_verified_at" => now(),
                "email" => $email,
                "phone" => '+255620350083',
                "saas_app_id" => $saasAppId,
                "password" => Hash::make($password),
            ]);
        }
    }
}
