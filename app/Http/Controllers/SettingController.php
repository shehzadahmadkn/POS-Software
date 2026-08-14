<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->all();
        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->extension();
            $logo->move(public_path('uploads/settings'), $logoName);
            \App\Models\Setting::updateOrCreate(
                ['key' => 'logo'],
                ['value' => 'uploads/settings/' . $logoName]
            );
        } elseif ($request->has('remove_logo')) {
            \App\Models\Setting::where('key', 'logo')->delete();
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = 'banner.' . $banner->extension();
            $banner->move(public_path('uploads/settings'), $bannerName);
            \App\Models\Setting::updateOrCreate(
                ['key' => 'banner'],
                ['value' => 'uploads/settings/' . $bannerName]
            );
        }

        if ($request->has('logo_text')) {
            \App\Models\Setting::updateOrCreate(
                ['key' => 'logo_text'],
                ['value' => $request->logo_text]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
