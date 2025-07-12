<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\DisableForeignKeys;

/**
 * Class AccessTableSeeder.
 */
class Version101TableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->call(RolesTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ClientSeeder::class);

        DB::commit();
    }
}
