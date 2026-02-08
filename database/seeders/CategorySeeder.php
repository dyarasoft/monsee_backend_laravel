<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $expenseCategories = [
            ['name' => 'Food & Beverage', 'icon' => 'food_beverage'],
            ['name' => 'Transportation', 'icon' => 'directions_car'],
            ['name' => 'Shopping', 'icon' => 'shopping_cart'],
            ['name' => 'Bills', 'icon' => 'credit_card'],
            ['name' => 'Entertainment', 'icon' => 'movie'],
            ['name' => 'Household', 'icon' => 'home'],
            ['name' => 'Health', 'icon' => 'self_improvement'],
            ['name' => 'Holiday', 'icon' => 'beach_access'],
            ['name' => 'Medical', 'icon' => 'medical_services'],
            ['name' => 'Education', 'icon' => 'school'],
            ['name' => 'Insurance', 'icon' => 'health_and_safety'],
            ['name' => 'Gifts & Donations', 'icon' => 'volunteer_activism'],
            ['name' => 'Travel', 'icon' => 'flight'],
            ['name' => 'Others', 'icon' => 'account_balance_wallet'],
        ];

        $incomeCategories = [
            ['name' => 'Salary', 'icon' => 'paid_rounded'],
            ['name' => 'Business', 'icon' => 'business_center'],
            ['name' => 'Gifts', 'icon' => 'gift'],
            ['name' => 'Dividend', 'icon' => 'local_atm'],
            ['name' => 'Interest', 'icon' => 'local_florist'],
            ['name' => 'Others', 'icon' => 'account_balance_wallet'],
        ];

        // Insert Expense Categories
        foreach ($expenseCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'type' => 'expense', 
                'user_id' => null,   
            ]);
        }

        // Insert Income Categories
        foreach ($incomeCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'type' => 'income',
                'user_id' => null,
            ]);
        }
    }
}