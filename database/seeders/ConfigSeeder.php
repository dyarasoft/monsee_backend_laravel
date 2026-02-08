<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            ['key' => 'version_android', 'value' => '1.0.0'],
            ['key' => 'version_ios', 'value' => '1.0.0'],
        ];

        foreach ($settings as $setting) {
            Config::create([
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
    }
}