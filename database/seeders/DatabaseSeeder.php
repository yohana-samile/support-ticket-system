<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        Model::unguard();
        $this->call(\Version100TableSeeder::class);
        $this->call(Version101TableSeeder::class);
        Model::reguard();
    }
}
