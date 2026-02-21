<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // Expense Category
        // ==========================================
        $categories = [
            [
                'name' => 'Food & Drink',
                'icon' => 'food_beverage', 
                'color' => '#F28B82',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Breakfast', 'icon' => 'bread_slice'],
                    ['name' => 'Lunch', 'icon' => 'burger'],
                    ['name' => 'Dinner', 'icon' => 'pizza_slice'],
                    ['name' => 'Snacks', 'icon' => 'cookie'],
                    ['name' => 'Beverages', 'icon' => 'local_bar'],
                    ['name' => 'Fruits', 'icon' => 'apple_whole'],
                    ['name' => 'Groceries', 'icon' => 'chart_shopping'],
                    ['name' => 'Coffee', 'icon' => 'coffee'],
                    ['name' => 'Eateries', 'icon' => 'local_cafe'],
                    ['name' => 'Street Food', 'icon' => 'local_cafe'],
                ]
            ],
            [
                'name' => 'Transportation',
                'icon' => 'directions_car',
                'color' => '#FFCC80',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Bus', 'icon' => 'local_bus'],
                    ['name' => 'Train', 'icon' => 'train'],
                    ['name' => 'Taxi', 'icon' => 'local_taxi'],
                    ['name' => 'Fuel', 'icon' => 'local_gas_station'],
                    ['name' => 'Parking', 'icon' => 'directions_bus'],
                    ['name' => 'Maintenance', 'icon' => 'build'],
                    ['name' => 'Tolls', 'icon' => 'tolls'],
                    ['name' => 'Flight', 'icon' => 'flight'],
                    ['name' => 'Online Transport', 'icon' => 'car_rental'],
                    ['name' => 'Insurance', 'icon' => 'build'],
                ]
            ],
            [
                'name' => 'Shopping',
                'icon' => 'shopping_cart',
                'color' => '#F48FB1',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Clothing', 'icon' => 'checkroom'],
                    ['name' => 'Electronics', 'icon' => 'devices_other'],
                    ['name' => 'Home', 'icon' => 'devices_other'],
                    ['name' => 'Books', 'icon' => 'menu_book'],
                    ['name' => 'Gifts', 'icon' => 'giftcard'],
                    ['name' => 'Beauty', 'icon' => 'local_mall'],
                    ['name' => 'Tools', 'icon' => 'shopping_bag'],
                    ['name' => 'Shoes', 'icon' => 'shopping_bag'],
                    ['name' => 'Online Shopping', 'icon' => 'shopping_bag'],
                ]
            ],
            [
                'name' => 'Housing',
                'icon' => 'home',
                'color' => '#BCAAA4',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Rent', 'icon' => 'house'],
                    ['name' => 'Utilities', 'icon' => 'electrical_services'],
                    ['name' => 'Maintenance', 'icon' => 'build'],
                    ['name' => 'Property Tax', 'icon' => 'account_balance'],
                    ['name' => 'Furniture', 'icon' => 'water_drop'],
                    ['name' => 'Service', 'icon' => 'wifi'],
                ]
            ],
            [
                'name' => 'Bills',
                'icon' => 'receipt',
                'color' => '#B0BEC5',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Electricity', 'icon' => 'house'],
                    ['name' => 'Water', 'icon' => 'electrical_services'],
                    ['name' => 'Internet', 'icon' => 'build'],
                    ['name' => 'Credit Card', 'icon' => 'account_balance'],
                    ['name' => 'Pulsa & Data', 'icon' => 'water_drop'],
                    ['name' => 'Insurance', 'icon' => 'wifi'],
                ]
            ],
            [
                'name' => 'Entertainment',
                'icon' => 'movie',
                'color' => '#CE93D8',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Movies', 'icon' => 'movie'],
                    ['name' => 'Streaming', 'icon' => 'play_circle_outline'],
                    ['name' => 'Games', 'icon' => 'videogame_asset'],
                    ['name' => 'Concerts', 'icon' => 'music_note'],
                    ['name' => 'Hobbies', 'icon' => 'sports_esports'],
                    ['name' => 'Travel', 'icon' => 'flight'],
                    ['name' => 'Events', 'icon' => 'event'],
                ]
            ],
            [
                'name' => 'Health',
                'icon' => 'local_hospital',
                'color' => '#FFAB91',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Doctor', 'icon' => 'local_hospital'],
                    ['name' => 'Pharmacy', 'icon' => 'medication'],
                    ['name' => 'Gym', 'icon' => 'fitness_center'],
                    ['name' => 'Dental', 'icon' => 'local_dining'],
                    ['name' => 'Insurance', 'icon' => 'local_dining'],
                    ['name' => 'Mental Health', 'icon' => 'local_hospital'],
                ]
            ],
            [
                'name' => 'Education',
                'icon' => 'school',
                'color' => '#FFE082',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Tuition', 'icon' => 'local_library'],
                    ['name' => 'Books', 'icon' => 'menu_book'],
                    ['name' => 'Courses', 'icon' => 'school'],
                    ['name' => 'School Supplies', 'icon' => 'local_dining'],
                    ['name' => 'Stationery', 'icon' => 'local_dining'],
                ]
            ],
            [
                'name' => 'Personal',
                'icon' => 'spa',
                'color' => '#B39DDB',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Haircut', 'icon' => 'person_outline'],
                    ['name' => 'Skincare', 'icon' => 'local_hospital'],
                    ['name' => 'Salon', 'icon' => 'local_hospital'],
                    ['name' => 'Spa', 'icon' => 'local_hospital'],
                    ['name' => 'Cosmetics', 'icon' => 'local_hospital'],
                    ['name' => 'Grooming', 'icon' => 'local_hospital'],
                ]
            ],
            [
                'name' => 'Family',
                'icon' => 'family_restroom',
                'color' => '#FF8A65', 
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Childcare', 'icon' => 'person_outline'],
                    ['name' => 'Toys', 'icon' => 'local_hospital'],
                    ['name' => 'Baby Gear', 'icon' => 'local_hospital'],
                    ['name' => 'Birthdays', 'icon' => 'local_hospital'],
                    ['name' => 'Events', 'icon' => 'local_hospital'],
                ]
            ],
            [
                'name' => 'Pets',
                'icon' => 'pets',
                'color' => '#A1887F',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Food', 'icon' => 'local_hospital'],
                    ['name' => 'Veterinary', 'icon' => 'local_hospital'],
                    ['name' => 'Supplies', 'icon' => 'local_hospital'],
                    ['name' => 'Grooming', 'icon' => 'local_hospital'],
                    ['name' => 'Insurance', 'icon' => 'local_hospital'],
                    ['name' => 'Training', 'icon' => 'local_hospital'],
                    ['name' => 'Toys', 'icon' => 'local_hospital'],
                ]
            ],
            [
                'name' => 'Others',
                'icon' => 'more_horiz',
                'color' => '#9E9E9E',
                'type' => 'expense',
                'subcategories' => [
                    ['name' => 'Adjustment', 'icon' => 'local_hospital'],
                    ['name' => 'Fees', 'icon' => 'local_hospital'],
                    ['name' => 'Donation', 'icon' => 'local_hospital'],
                    ['name' => 'Zakat', 'icon' => 'local_hospital'],
                    ['name' => 'Fines', 'icon' => 'local_hospital'],
                    ['name' => 'Taxes', 'icon' => 'local_hospital'],
                ]
            ],  

            // ==========================================
            // Income Category
            // ==========================================
            [
                'name' => 'Salary',
                'icon' => 'payments',
                'color' => '#A5D6A7',
                'type' => 'income',
                'subcategories' => [
                    ['name' => 'Full-time Job', 'icon' => 'work'],
                    ['name' => 'Freelance', 'icon' => 'computer'],
                    ['name' => 'Bonus', 'icon' => 'work'],
                    ['name' => 'Overtime', 'icon' => 'work'],
                ]
            ],
            [
                'name' => 'Bisnis',
                'icon' => 'storefront',
                'color' => '#90CAF9',
                'type' => 'income',
                'subcategories' => [
                    ['name' => 'Sales', 'icon' => 'pie_chart'],
                    ['name' => 'Services', 'icon' => 'show_chart'],
                    ['name' => 'Profit', 'icon' => 'show_chart'],
                ]
            ],
            [
                'name' => 'Investment',
                'icon' => 'trending_up',
                'color' => '#80CBC4', 
                'type' => 'income',
                'subcategories' => [
                    ['name' => 'Dividends', 'icon' => 'pie_chart'],
                    ['name' => 'Capital Gain', 'icon' => 'show_chart'],
                    ['name' => 'Interest', 'icon' => 'show_chart'],
                ]
            ],
            [
                'name' => 'Gifts',
                'icon' => 'card_giftcard',
                'color' => '#C5E1A5',
                'type' => 'income',
                'subcategories' => [
                    ['name' => 'Birthday', 'icon' => 'pie_chart'],
                    ['name' => 'Allowance', 'icon' => 'cake'],
                    ['name' => 'Religious Holiday', 'icon' => 'local_hospital'],
                ]
            ],
            [
                'name' => 'Others',
                'icon' => 'more_horiz',
                'color' => '#81D4FA',
                'type' => 'income',
                'subcategories' => [
                    ['name' => 'Refunds', 'icon' => 'pie_chart'],
                    ['name' => 'Grants', 'icon' => 'cake'],
                    ['name' => 'Lottery', 'icon' => 'local_hospital'],
                    ['name' => 'Selling', 'icon' => 'local_hospital'],
                ]
            ],
        ];

        // Looping untuk menyimpan data ke database
        foreach ($categories as $catData) {
            // 1. Buat Parent Category
            $parentCategory = Category::create([
                'name' => $catData['name'],
                'icon' => $catData['icon'],
                'color' => $catData['color'], // Pastikan color disimpan di parent
                'type' => $catData['type'],
                'parent_id' => null, // Ini adalah parent
                'user_id' => null, // null berarti ini adalah kategori default system (global)
            ]);

            // 2. Buat Sub-Categories (jika ada)
            if (isset($catData['subcategories']) && is_array($catData['subcategories'])) {
                foreach ($catData['subcategories'] as $subCatData) {
                    Category::create([
                        'name' => $subCatData['name'],
                        'icon' => $subCatData['icon'],
                        'color' => $parentCategory->color, // <-- Pewarisan warna ke sub-kategori
                        'type' => $catData['type'],
                        'parent_id' => $parentCategory->id,
                        'user_id' => null,
                    ]);
                }
            }
        }
    }
}