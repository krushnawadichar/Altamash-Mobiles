<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settings = $this->settingService->getAllSettings();
        $settingsGrouped = $settings->groupBy('group');
        return view('admin.settings.index', compact('settingsGrouped'));
    }

    public function update(Request $request)
    {
        $this->settingService->updateSettings($request->all());
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}