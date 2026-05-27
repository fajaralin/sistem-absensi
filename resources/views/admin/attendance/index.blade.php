@extends('layouts.app')

@section('title', 'Rekap Kehadiran')
@section('page_title', 'Rekap Kehadiran Siswa')

@section('content')
<div class="space-y-6" x-data="{ manualModalOpen: false }">
    
    <!-- Filter Card & Report Exports -->
    <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl">
        <div class="card-body p-6 md:p-8 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-start gap-3">
                    <div class="p-2.5 bg-primary/10 rounded-xl text-primary mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-800 text-lg">
                            Rekapitulasi Presensi
                        </h3>
                        <p class="text-xs font-semibold text-slate-500 mt-0.5">Filter riwayat kehadiran dan lakukan pencatatan manual secara instan</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Manual presence trigger -->
                    <button type="button" @click="manualModalOpen = true" 
                        class="btn btn-sm bg-primary/5 hover:bg-primary/10 text-primary border-primary/20 hover:border-primary/30 normal-case rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Catat Manual
                    </button>

                    <!-- Export CSV -->
                    <a href="{{ route('admin.attendance.export.csv', request()->all()) }}" 
                        class="btn btn-sm bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200/60 hover:border-emerald-300 normal-case rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Excel (CSV)
                    </a>

                    <!-- Printable PDF -->
                    <a href="{{ route('admin.attendance.export.pdf', request()->all()) }}" target="_blank"
                        class="btn btn-sm bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 hover:border-slate-300 normal-case rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <!-- Date Filter -->
                <div class="form-control w-full space-y-2">
                    <label for="date" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Tanggal</label>
                    <input type="text" id="date" name="date" value="{{ $filters['date'] }}" placeholder="Pilih Tanggal..."
                        class="datepicker input input-bordered input-sm w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                </div>

                <!-- Status Filter -->
                <div class="form-control w-full space-y-2">
                    <label for="status" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Status Kehadiran</label>
                    <select id="status" name="status"
                        class="select select-bordered select-sm w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ $filters['status'] === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="sakit" {{ $filters['status'] === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ $filters['status'] === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="alpha" {{ $filters['status'] === 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>

                <!-- Search Filter -->
                <div class="form-control w-full space-y-2">
                    <label for="search" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Cari NISN / Nama</label>
                    <input type="text" id="search" name="search" placeholder="Cari nama atau NISN..." value="{{ $filters['search'] }}"
                        class="input input-bordered input-sm w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary rounded-xl w-full h-11 text-xs font-bold gap-2 shadow-md shadow-primary/10 hover:shadow-primary/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/60 text-slate-500 font-bold text-[11px] uppercase tracking-wider">
                        <th class="px-6 py-4">Nama / NISN</th>
                        <th class="px-6 py-4">Jurusan / Kelas</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Jam Masuk</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Confidence</th>
                        <th class="px-6 py-4">Snapshot</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-semibold text-sm text-neutral-700 divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/45 transition-colors">
                            <!-- Student identity -->
                            <td class="px-6 py-4">
                                <span class="block text-neutral-800 font-bold leading-tight">{{ $att->student->user->name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold tracking-wide">NISN: {{ $att->student->nisn }}</span>
                            </td>

                            <!-- Dept & Class -->
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-slate-700">{{ $att->student->department }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold">Kelas: {{ $att->student->class_name }}</span>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                {{ $att->date->translatedFormat('d M Y') }}
                            </td>

                            <!-- Check-in time -->
                            <td class="px-6 py-4 text-neutral-800 font-bold text-xs">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '-' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($att->status === 'hadir')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">hadir</span>
                                @elseif($att->status === 'sakit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">sakit</span>
                                @elseif($att->status === 'izin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">izin</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-100">alpha</span>
                                @endif
                            </td>

                            <!-- Method -->
                            <td class="px-6 py-4 text-xs">
                                @if($att->method === 'face_recognition')
                                    <span class="bg-primary/5 text-primary border border-primary/10 rounded-lg px-2.5 py-1 text-[11px] font-bold inline-flex items-center gap-1.5 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Scan Wajah
                                    </span>
                                @else
                                    <span class="bg-slate-50 text-slate-600 border border-slate-200/80 rounded-lg px-2.5 py-1 text-[11px] font-bold inline-flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Manual Admin
                                    </span>
                                @endif
                            </td>

                            <!-- Confidence Score -->
                            <td class="px-6 py-4 text-xs font-bold text-primary">
                                {{ $att->confidence ? number_format($att->confidence * 100, 1) . '%' : '-' }}
                            </td>

                            <!-- Snap Shot Webcam Hoverable Image -->
                            <td class="px-6 py-4">
                                @if($att->face_image_path)
                                    <div class="relative w-9 h-9 rounded-xl border border-slate-200 overflow-hidden hover:scale-110 cursor-zoom-in transition-all group">
                                        <img src="{{ asset('storage/' . $att->face_image_path) }}" alt="Snapshot" class="w-full h-full object-cover">
                                        <!-- Large hover preview -->
                                        <div class="hidden group-hover:block absolute bottom-10 right-0 z-40 bg-white border border-slate-200 p-2 rounded-2xl w-36 shadow-xl transition-all duration-200 animate-fade-in">
                                            <img src="{{ asset('storage/' . $att->face_image_path) }}" class="w-full h-auto rounded-xl">
                                            <div class="text-[9px] text-center font-bold text-slate-400 mt-1.5 uppercase tracking-wider">Snapshot Asli</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">No Snap</span>
                                @endif
                            </td>

                            <!-- Action Column (Delete button) -->
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.attendance.destroy', $att->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data presensi ini secara permanen?');"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="btn btn-square btn-sm btn-outline border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-slate-400 rounded-xl transition-all"
                                        title="Hapus Presensi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-slate-400 font-bold">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-50 text-slate-300 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5m16 0L13 16l-1 2.5L10 16l-3-2.5" />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400 tracking-wide">Tidak ada data presensi yang cocok dengan filter pencarian.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- POPUP DIALOG MODAL FOR MANUAL ATTENDANCE -->
    <div x-show="manualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md" style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        <div class="card w-full max-w-md bg-white border border-slate-200/60 shadow-2xl rounded-[2rem] overflow-hidden relative" @click.away="manualModalOpen = false">
            <!-- Top Primary Color Bar -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-blue-500 to-primary"></div>
            
            <div class="card-body p-6 md:p-8 space-y-4">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-primary/10 rounded-lg text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-neutral-800 text-base">
                            Pencatatan Presensi Manual
                        </h3>
                    </div>
                    <button type="button" @click="manualModalOpen = false" class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-neutral-800">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.attendance.manual') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Student Selection -->
                    <div class="form-control w-full space-y-2">
                        <label for="student_id" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Pilih Siswa</label>
                        <select id="student_id" name="student_id" required
                            class="select select-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            <option value="">Pilih Siswa...</option>
                            @foreach($students as $stud)
                                <option value="{{ $stud->id }}">{{ $stud->user->name }} (NISN: {{ $stud->nisn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="form-control w-full space-y-2">
                        <label for="manual_date" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Tanggal</label>
                        <input type="text" id="manual_date" name="date" required value="{{ today()->toDateString() }}" placeholder="Pilih Tanggal..."
                            class="datepicker input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Status -->
                        <div class="form-control w-full space-y-2">
                            <label for="manual_status" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Status</label>
                            <select id="manual_status" name="status" required
                                class="select select-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                                <option value="hadir">Hadir</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>

                        <!-- Check-in Time -->
                        <div class="form-control w-full space-y-2">
                            <label for="manual_check_in" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Jam Masuk (WIB)</label>
                            <input type="text" id="manual_check_in" name="check_in" placeholder="08:00:00"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-control w-full space-y-2">
                        <label for="manual_notes" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Catatan / Keterangan</label>
                        <textarea id="manual_notes" name="notes" rows="2" placeholder="Tulis catatan (misal: Sakit Demam, surat terlampir)..."
                            class="textarea textarea-bordered w-full rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200 p-3"></textarea>
                    </div>

                    <!-- Footer buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" @click="manualModalOpen = false" 
                            class="btn btn-sm bg-slate-50 hover:bg-slate-100 text-slate-600 border-slate-200 rounded-xl px-5 h-10 normal-case text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-xl px-5 h-10 normal-case text-xs font-bold shadow-md shadow-primary/20">
                            Simpan Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

