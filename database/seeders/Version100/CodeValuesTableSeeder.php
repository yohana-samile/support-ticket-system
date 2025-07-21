<?php

use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use App\Models\System\CodeValue;

class CodeValuesTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys("code_values");
        $this->delete('code_values');

        $allCodeValues = [
            'User Logs' => [
                ['reference' => 'ULLGI', 'name' => 'Log In'],
                ['reference' => 'ULLGO', 'name' => 'Log Out'],
                ['reference' => 'ULFLI', 'name' => 'Failed Log In'],
                ['reference' => 'ULPRS', 'name' => 'Password Reset'],
                ['reference' => 'ULULC', 'name' => 'User Lockout'],
            ],
            'Auth User Type' => [
                ['reference' => 'USER000', 'name' => 'Super Admin'],
                ['reference' => 'USER001', 'name' => 'Case Worker'],
                ['reference' => 'USER002', 'name' => 'Reporter'],
                ['reference' => 'USER003', 'name' => 'Law Enforcement'],
            ],
            'Gender' => [
                ['reference' => 'GENDER01', 'name' => 'Male', 'is_system_defined' => 0],
                ['reference' => 'GENDER02', 'name' => 'Female', 'is_system_defined' => 0],
            ],
            'Ticket Status' => [
                ['reference' => 'STATUS01', 'name' => 'open', 'is_system_defined' => 1],
                ['reference' => 'STATUS02', 'name' => 'in_progress', 'is_system_defined' => 1],
                ['reference' => 'STATUS03', 'name' => 'resolved', 'is_system_defined' => 1],
                ['reference' => 'STATUS04', 'name' => 'closed', 'is_system_defined' => 1],
                ['reference' => 'STATUS05', 'name' => 'reopen', 'is_system_defined' => 1],
                ['reference' => 'STATUS06', 'name' => 'escalated', 'is_system_defined' => 1],
            ],

            'Ticket Priority' => [
                ['reference' => 'PRIORITY01', 'name' => 'low', 'is_system_defined' => 1],
                ['reference' => 'PRIORITY02', 'name' => 'medium', 'is_system_defined' => 1],
                ['reference' => 'PRIORITY03', 'name' => 'high', 'is_system_defined' => 1],
                ['reference' => 'PRIORITY04', 'name' => 'critical', 'is_system_defined' => 1],
            ],

            'Mobile Operator' => [
                ['reference' => 'OPERATOR01', 'name' => 'Vodacom', 'is_system_defined' => 1],
                ['reference' => 'OPERATOR02', 'name' => 'Airtel', 'is_system_defined' => 1],
                ['reference' => 'OPERATOR03', 'name' => 'Yas', 'is_system_defined' => 1],
                ['reference' => 'OPERATOR04', 'name' => 'Halotel', 'is_system_defined' => 1],
                ['reference' => 'OPERATOR05', 'name' => 'All', 'is_system_defined' => 1],
            ]
        ];

        foreach ($allCodeValues as $codeName => $values) {
            $codeId = \App\Models\System\Code::query()->where('name', $codeName)->value('id');
            $sort = 1;

            foreach ($values as $value) {
                CodeValue::withTrashed()->updateOrCreate(
                    ['reference' => $value['reference']],
                    [
                        'code_id' => $codeId,
                        'name' => $value['name'],
                        'lang' => null,
                        'description' => '',
                        'sort' => $sort++,
                        'isactive' => $value['isactive'] ?? 1,
                        'is_system_defined' => $value['is_system_defined'] ?? 1,
                    ]
                );
            }
        }
        $this->enableForeignKeys("code_values");
    }
}
