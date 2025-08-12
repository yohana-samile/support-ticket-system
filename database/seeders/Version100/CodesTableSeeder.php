<?php

use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;

class CodesTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys("codes");
        $this->delete('codes');

        $codes = [
            ['name' => 'User Logs', 'lang' => 'user_log', 'is_system_defined' => 1],
            ['name' => 'Auth User Type', 'lang' => 'auth_user_type', 'is_system_defined' => 1],
            ['name' => 'Gender', 'lang' => 'gender', 'is_system_defined' => 0],
            ['name' => 'Ticket Status', 'lang' => 'ticket_status', 'is_system_defined' => 1],
            ['name' => 'Ticket Priority', 'lang' => 'ticket_priority', 'is_system_defined' => 1],
            ['name' => 'Mobile Operator', 'lang' => 'mobile_operator', 'is_system_defined' => 1],
            ['name' => 'Sticker Status', 'lang' => 'sticker_status', 'is_system_defined' => 1],
        ];

        foreach ($codes as $code) {
            \App\Models\System\Code::updateOrCreate(['name' => $code['name']], [
                'lang' => $code['lang'],
                'is_system_defined' => $code['is_system_defined'],
            ]);
        }
        $this->enableForeignKeys("codes");
    }
}
