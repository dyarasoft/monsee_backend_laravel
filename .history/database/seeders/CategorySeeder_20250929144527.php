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
                'icon' => 'transport_icon',
                'color_hex' => '#2196F3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Food',
                'icon' => 'food_icon',
                'color_hex' => '#FF9800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Bills',
                'icon' => 'bills_icon',
                'color_hex' => '#F44336',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Entertainment',
                'icon' => 'entertainment_icon',
                'color_hex' => '#9C27B0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'name' => 'Income',
                'icon' => 'income_icon',
                'color_hex' => '#4CAF50',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
