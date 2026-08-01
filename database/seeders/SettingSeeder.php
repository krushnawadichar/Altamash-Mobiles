<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'shop_name', 'value' => 'Altamash Mobiles', 'group' => 'general'],
            ['key' => 'shop_logo', 'value' => null, 'group' => 'general'],
            ['key' => 'gst_number', 'value' => '1234567890', 'group' => 'general'],
            ['key' => 'shop_address', 'value' => 'Main Market, Lahore', 'group' => 'general'],
            ['key' => 'shop_phone', 'value' => '0300-1234567', 'group' => 'general'],
            ['key' => 'shop_email', 'value' => 'info@altamashmobiles.com', 'group' => 'general'],
            ['key' => 'invoice_prefix', 'value' => 'INV-', 'group' => 'invoice'],
            ['key' => 'currency', 'value' => 'PKR', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'group' => 'general'],
            ['key' => 'default_gst', 'value' => '18', 'group' => 'tax'],
        ];

        foreach ($settings as $setting) {
            Setting::create([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'group' => $setting['group'],
                'is_active' => true,
            ]);
        }
    }
}