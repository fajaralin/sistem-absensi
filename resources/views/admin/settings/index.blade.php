@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan')
@section('header_title', 'Pengaturan Sistem ⚙️')
@section('header_subtitle', 'Atur jam presensi, batas keterlambatan, dan identitas sekolah untuk laporan resmi.')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Pengaturan Jam Presensi -->
    <div class="card bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm">
        <div class="flex items-start gap-3.5 mb-6">
            <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600 mt-0.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-neutral-800 tracking-tight">Jam Presensi & Keterlambatan</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Siswa yang melakukan scan wajah setelah melewati "Batas Jam Telat" akan otomatis dicatat dengan status <span class="font-bold text-amber-600">TELAT</span>, bukan hadir.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Jam Mulai Presensi</label>
                <input type="time" name="attendance_start_time"
                       value="{{ old('attendance_start_time', $settings['attendance_start_time'] ?? '07:00') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                <p class="text-xs text-slate-400 mt-1.5">Waktu gerbang presensi mulai dibuka.</p>
                @error('attendance_start_time')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Batas Jam Telat</label>
                <input type="time" name="attendance_late_threshold"
                       value="{{ old('attendance_late_threshold', $settings['attendance_late_threshold'] ?? '07:30') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                <p class="text-xs text-slate-400 mt-1.5">Scan wajah setelah jam ini otomatis berstatus "Telat".</p>
                @error('attendance_late_threshold')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <!-- Identitas Sekolah (Kop Surat) -->
    <div class="card bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm">
        <div class="flex items-start gap-3.5 mb-6">
            <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 mt-0.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-neutral-800 tracking-tight">Identitas Sekolah & Kop Surat</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Informasi ini akan tampil otomatis pada Kop Surat laporan presensi (Export PDF / Cetak).
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Sekolah</label>
                <input type="text" name="school_name"
                       value="{{ old('school_name', $settings['school_name'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('school_name')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Sekolah</label>
                <input type="text" name="school_address"
                       value="{{ old('school_address', $settings['school_address'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('school_address')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Telepon</label>
                <input type="text" name="school_phone"
                       value="{{ old('school_phone', $settings['school_phone'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('school_phone')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="school_email"
                       value="{{ old('school_email', $settings['school_email'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('school_email')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kepala Sekolah</label>
                <input type="text" name="principal_name"
                       value="{{ old('principal_name', $settings['principal_name'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('principal_name')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">NIP Kepala Sekolah</label>
                <input type="text" name="principal_nip"
                       value="{{ old('principal_nip', $settings['principal_nip'] ?? '') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('principal_nip')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-8 py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all cursor-pointer flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
