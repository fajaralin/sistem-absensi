<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use App\Services\LogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;
    protected $logService;

    /**
     * SettingController constructor.
     */
    public function __construct(SettingService $settingService, LogService $logService)
    {
        $this->settingService = $settingService;
        $this->logService = $logService;
    }

    /**
     * Tampilkan halaman pengaturan sistem.
     */
    public function index()
    {
        $settings = $this->settingService->getAll();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Simpan perubahan pengaturan sistem.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'attendance_start_time' => 'required|date_format:H:i',
            'attendance_late_threshold' => 'required|date_format:H:i|after:attendance_start_time',
            'school_name' => 'required|string|max:150',
            'school_address' => 'nullable|string|max:255',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:150',
            'principal_name' => 'nullable|string|max:150',
            'principal_nip' => 'nullable|string|max:100',
            'attendance_location_enabled' => 'required|string|in:yes,no',
            'attendance_location_name' => 'nullable|string|max:255',
            'attendance_latitude' => 'nullable|numeric|between:-90,90',
            'attendance_longitude' => 'nullable|numeric|between:-180,180',
            'attendance_radius' => 'nullable|integer|min:1',
        ]);

        $this->settingService->updateSettings($validated);

        $this->logService->logActivity(
            auth()->id(),
            'update_settings',
            'Admin memperbarui pengaturan sistem (jam presensi, lokasi GPS & identitas sekolah).'
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil disimpan!');
    }
}
