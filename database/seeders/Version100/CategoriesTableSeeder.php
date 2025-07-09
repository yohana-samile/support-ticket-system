<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Payment Issue', 'description' => 'Problems related to payments'],
            ['name' => 'Technical Issue', 'description' => 'Technical problems with the system'],
            ['name' => 'Account Problem', 'description' => 'Issues with user accounts'],
            ['name' => 'Feature Request', 'description' => 'Requests for new features'],
            ['name' => 'General Inquiry', 'description' => 'General questions'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], [
                'description' => $category['description'],
            ]);
        }
    }
}
