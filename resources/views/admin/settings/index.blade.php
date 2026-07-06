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

    <!-- Pengaturan Lokasi Absensi (GPS) -->
    <div class="card bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm">
        <div class="flex items-start gap-3.5 mb-6">
            <div class="p-3 bg-amber-50 rounded-2xl text-amber-600 mt-0.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-neutral-800 tracking-tight">Lokasi Presensi & Geolocation</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">
                    Batasi wilayah tempat siswa dapat melakukan presensi dengan menentukan koordinat lobi/sekolah dan radius toleransi.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Wajibkan Presensi di Lokasi (GPS)</label>
                <select name="attendance_location_enabled" id="attendance_location_enabled"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                    <option value="no" {{ old('attendance_location_enabled', $settings['attendance_location_enabled'] ?? 'no') === 'no' ? 'selected' : '' }}>Nonaktif (Bebas lokasi / Kiosk Fisik Standalone)</option>
                    <option value="yes" {{ old('attendance_location_enabled', $settings['attendance_location_enabled'] ?? 'no') === 'yes' ? 'selected' : '' }}>Aktif (Wajib di lokasi sekolah)</option>
                </select>
                @error('attendance_location_enabled')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lokasi</label>
                <input type="text" name="attendance_location_name"
                       value="{{ old('attendance_location_name', $settings['attendance_location_name'] ?? 'Lobi Sekolah SMAN 1 Utama') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('attendance_location_name')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Radius Toleransi (Meter)</label>
                <input type="number" name="attendance_radius"
                       value="{{ old('attendance_radius', $settings['attendance_radius'] ?? '100') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('attendance_radius')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Latitude</label>
                <input type="text" id="attendance_latitude" name="attendance_latitude"
                       value="{{ old('attendance_latitude', $settings['attendance_latitude'] ?? '-6.200000') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('attendance_latitude')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Longitude</label>
                <input type="text" id="attendance_longitude" name="attendance_longitude"
                       value="{{ old('attendance_longitude', $settings['attendance_longitude'] ?? '106.816666') }}"
                       class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-semibold text-slate-700">
                @error('attendance_longitude')<p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-bold text-slate-700">Pilih Titik Lokasi Pada Peta</label>
                <p class="text-xs text-slate-400 font-semibold">Klik di area mana saja atau seret penanda merah untuk menyinkronkan koordinat latitude/longitude secara otomatis.</p>
                <div id="map" class="h-72 w-full rounded-2xl border border-slate-200/80 shadow-inner z-0"></div>
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

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Fix Leaflet map sizing issues */
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let latInput = document.getElementById('attendance_latitude');
        let lngInput = document.getElementById('attendance_longitude');
        
        let defaultLat = parseFloat(latInput.value) || -6.200000;
        let defaultLng = parseFloat(lngInput.value) || 106.816666;
        
        let map = L.map('map').setView([defaultLat, defaultLng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        let marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);
        
        function updateInputs(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(6);
            lngInput.value = parseFloat(lng).toFixed(6);
        }
        
        marker.on('dragend', function(e) {
            let latlng = marker.getLatLng();
            updateInputs(latlng.lat, latlng.lng);
        });
        
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
        
        // Input listener manual
        latInput.addEventListener('change', function() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], map.getZoom());
            }
        });
        
        lngInput.addEventListener('change', function() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], map.getZoom());
            }
        });

        // Geolocate device if coordinates are default
        if (defaultLat === -6.200000 && defaultLng === 106.816666) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    updateInputs(lat, lng);
                }, function(err) {
                    console.log("Device Geolocation denied or unavailable");
                });
            }
        }
    });
</script>
@endsection
@endsection
