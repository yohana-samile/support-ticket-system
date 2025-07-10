<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\DisableForeignKeys;

/**
 * Class AccessTableSeeder.
 */
class Version100TableSeeder extends Seeder
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

        $this->call(CodesTableSeeder::class);
        $this->call(CodeValuesTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ChannelSeeder::class);

        $this->call(PermissionGroupTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);

        DB::commit();
    }
}
