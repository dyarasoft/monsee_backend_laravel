<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'user_id' => null,
                'name' => 'Transport',
                'icon' => 'directions_car',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Food',
                'icon' => 'food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Bills',
                'icon' => 'credit_card',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Entertainment',
                'icon' => 'movie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Shopping',
                'icon' => 'shopping_cart',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Education',
                'icon' => 'school',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Insurance',
                'icon' => 'health_and_safety',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Insurance',
                'icon' => 'health_and_safety',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
