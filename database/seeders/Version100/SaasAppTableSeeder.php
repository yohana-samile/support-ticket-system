<?php

use App\Models\SaasApp;
use Illuminate\Database\Seeder;

class SaasAppTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $saasApps = [
            ['name' => 'Bulk SMS Solution', 'abbreviation' => 'Nextsms'],
            ['name' => 'NextAccounting Account Software', 'abbreviation' => 'NextAccounting'],
            ['name' => 'Petrol Station Management System', 'abbreviation' => 'PSMS'],
            ['name' => 'Email Manager', 'abbreviation' => 'PEPE'],
            ['name' => 'NextHost Reliable Web Hosting', 'abbreviation' => 'NextHost'],
            ['name' => 'NextCrm', 'abbreviation' => 'NextCrm'],
            ['name' => 'RafikiSite', 'abbreviation' => 'RafikiSite'],
        ];

        foreach ($saasApps as $saasApp) {
            SaasApp::updateOrCreate(['name' => $saasApp['name']], [
                'abbreviation' => $saasApp['abbreviation'],
            ]);
        }
    }
}
