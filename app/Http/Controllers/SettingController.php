<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS & Repair Centre'),
            'shop_tagline' => Setting::get('shop_tagline', 'Sales & Service Station'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_email' => Setting::get('shop_email', ''),
            'gst_number' => Setting::get('gst_number', ''),
            'currency_symbol' => Setting::get('currency_symbol', '₹'),
            'invoice_prefix' => Setting::get('invoice_prefix', 'INV'),
            'purchase_prefix' => Setting::get('purchase_prefix', 'PUR'),
            'repair_prefix' => Setting::get('repair_prefix', 'REP'),
            'default_tax_percent' => Setting::get('default_tax_percent', '18'),
            'thermal_printer_width' => Setting::get('thermal_printer_width', '80mm'),
            'invoice_footer' => Setting::get('invoice_footer', 'Thank you for your business!'),
            'terms_conditions' => Setting::get('terms_conditions', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log('SETTINGS_UPDATED', 'settings', null, 'Shop and invoice settings updated.');

        return back()->with('success', 'Shop settings updated successfully.');
    }
}
