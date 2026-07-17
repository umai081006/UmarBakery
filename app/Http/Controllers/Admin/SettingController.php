<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request, \App\Services\CloudinaryService $cloudinaryService)
    {
        // Data teks biasa
        $data = $request->except(['_token', '_method', 'hero_bg_file', 'about_img_file', 'process_img_file']);

        // Handle file uploads
        $filesToUpload = [
            'hero_bg' => 'hero_bg_file',
            'about_img' => 'about_img_file',
            'process_img' => 'process_img_file'
        ];

        foreach ($filesToUpload as $settingKey => $inputName) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $uploadResult = $cloudinaryService->upload($file, 'settings');
                if ($uploadResult) {
                    $data[$settingKey] = $uploadResult;
                }
            }
        }

        // Update database
        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => 'general']
                );
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
