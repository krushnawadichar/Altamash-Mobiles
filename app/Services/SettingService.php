<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function getAllSettings()
    {
        return Setting::all();
    }

    public function getSetting($key)
    {
        return Setting::where('key', $key)->first();
    }

    public function getSettingValue($key, $default = null)
    {
        $setting = $this->getSetting($key);
        return $setting ? $setting->value : $default;
    }

    public function updateSettings(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key === '_token' || $key === '_method') {
                continue;
            }

            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Handle file uploads
                if ($key === 'shop_logo' && $value instanceof \Illuminate\Http\UploadedFile) {
                    if ($setting->value) {
                        Storage::delete('public/' . $setting->value);
                    }
                    $value = $this->uploadLogo($value);
                }
                
                $setting->update(['value' => $value]);
            }
        }

        return true;
    }

    public function updateSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        
        if (!$setting) {
            $setting = Setting::create([
                'key' => $key,
                'value' => $value,
                'group' => 'general',
            ]);
        } else {
            $setting->update(['value' => $value]);
        }

        return $setting;
    }

    protected function uploadLogo($logo)
    {
        $filename = time() . '_logo.' . $logo->getClientOriginalExtension();
        return $logo->storeAs('settings', $filename, 'public');
    }

    public function getGeneralSettings()
    {
        return Setting::where('group', 'general')->get()->pluck('value', 'key');
    }

    public function getInvoiceSettings()
    {
        return Setting::where('group', 'invoice')->get()->pluck('value', 'key');
    }

    public function getTaxSettings()
    {
        return Setting::where('group', 'tax')->get()->pluck('value', 'key');
    }

    public function getShopInfo()
    {
        $settings = $this->getGeneralSettings();
        
        return [
            'name' => $settings['shop_name'] ?? 'Altamash Mobiles',
            'logo' => $settings['shop_logo'] ?? null,
            'address' => $settings['shop_address'] ?? '',
            'phone' => $settings['shop_phone'] ?? '',
            'email' => $settings['shop_email'] ?? '',
            'gst_number' => $settings['gst_number'] ?? '',
            'currency' => $settings['currency'] ?? 'PKR',
            'timezone' => $settings['timezone'] ?? 'Asia/Karachi',
        ];
    }

    public function getInvoicePrefix()
    {
        return $this->getSettingValue('invoice_prefix', 'INV-');
    }

    public function getDefaultGst()
    {
        return $this->getSettingValue('default_gst', 18);
    }

    public function getCurrency()
    {
        return $this->getSettingValue('currency', 'PKR');
    }

    public function getShopName()
    {
        return $this->getSettingValue('shop_name', 'Altamash Mobiles');
    }

    public function getShopLogo()
    {
        $logo = $this->getSettingValue('shop_logo');
        return $logo ? Storage::url($logo) : null;
    }

    public function getShopAddress()
    {
        return $this->getSettingValue('shop_address', '');
    }

    public function getShopPhone()
    {
        return $this->getSettingValue('shop_phone', '');
    }

    public function getShopEmail()
    {
        return $this->getSettingValue('shop_email', '');
    }

    public function getGstNumber()
    {
        return $this->getSettingValue('gst_number', '');
    }

    public function getTimezone()
    {
        return $this->getSettingValue('timezone', 'Asia/Karachi');
    }

    public function initializeDefaultSettings()
    {
        $defaults = [
            // General Settings
            ['key' => 'shop_name', 'value' => 'Altamash Mobiles', 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_logo', 'value' => null, 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_address', 'value' => 'Main Market, Lahore, Pakistan', 'group' => 'general', 'type' => 'text'],
            ['key' => 'shop_phone', 'value' => '+92-300-1234567', 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_email', 'value' => 'info@altamashmobiles.com', 'group' => 'general', 'type' => 'string'],
            ['key' => 'gst_number', 'value' => '1234567890', 'group' => 'general', 'type' => 'string'],
            ['key' => 'currency', 'value' => 'PKR', 'group' => 'general', 'type' => 'string'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'group' => 'general', 'type' => 'string'],
            
            // Invoice Settings
            ['key' => 'invoice_prefix', 'value' => 'INV-', 'group' => 'invoice', 'type' => 'string'],
            ['key' => 'purchase_prefix', 'value' => 'PO-', 'group' => 'invoice', 'type' => 'string'],
            ['key' => 'repair_prefix', 'value' => 'REP-', 'group' => 'invoice', 'type' => 'string'],
            
            // Tax Settings
            ['key' => 'default_gst', 'value' => '18', 'group' => 'tax', 'type' => 'string'],
            ['key' => 'tax_inclusive', 'value' => '1', 'group' => 'tax', 'type' => 'boolean'],
        ];

        foreach ($defaults as $default) {
            Setting::firstOrCreate(
                ['key' => $default['key']],
                [
                    'value' => $default['value'],
                    'group' => $default['group'],
                    'type' => $default['type'],
                    'is_active' => true,
                ]
            );
        }

        return true;
    }

    public function getSettingsByGroup($group)
    {
        return Setting::where('group', $group)->get()->pluck('value', 'key');
    }

    public function deleteSetting($key)
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            // Delete file if it's a logo
            if ($key === 'shop_logo' && $setting->value) {
                Storage::delete('public/' . $setting->value);
            }
            return $setting->delete();
        }
        
        return false;
    }

    public function createBackup()
    {
        $settings = Setting::all();
        $backup = [
            'created_at' => now()->format('Y-m-d H:i:s'),
            'settings' => $settings->toArray(),
        ];
        
        $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';
        $path = storage_path('app/backups/' . $filename);
        
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        file_put_contents($path, json_encode($backup, JSON_PRETTY_PRINT));
        
        return $filename;
    }

    public function restoreBackup($filePath)
    {
        $content = file_get_contents($filePath);
        $backup = json_decode($content, true);
        
        if (!$backup || !isset($backup['settings'])) {
            return false;
        }
        
        foreach ($backup['settings'] as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'is_active' => $setting['is_active'] ?? true,
                ]
            );
        }
        
        return true;
    }

    public function getBackupFiles()
    {
        $path = storage_path('app/backups');
        if (!is_dir($path)) {
            return [];
        }
        
        $files = glob($path . '/*.json');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'created_at' => filemtime($file),
            ];
        }
        
        return $backups;
    }
}