<?php
// database/seeders/SettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'techBook', 'type' => 'text', 'group' => 'general'],
            ['key' => 'primary_color', 'value' => '#667eea', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#764ba2', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'contact_email', 'value' => 'admin@techbook.com', 'type' => 'email', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}