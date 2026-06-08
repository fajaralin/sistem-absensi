@extends('layouts.app')

@section('title', 'Rekap per Kelas/Jurusan')
@section('page_title', 'Rekap Kehadiran')
@section('header_title', 'Rekap Kehadiran per Kelas/Jurusan 📊')
@section('header_subtitle', 'Lihat agregat kehadiran setiap kelas/jurusan dan persentase kehadirannya pada rentang tanggal tertentu.')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl">
        <div class="card-body p-6 md:p-8 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-start gap-3">
                    <div class="p-2.5 bg-primary/10 rounded-xl text-primary mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-800 text-lg">
                            Rekap Kehadiran per Kelas/Jurusan
                        </h3>
                        <p class="text-xs font-semibold text-slate-500 mt-0.5">Pilih rentang tanggal untuk menampilkan agregat kehadiran tiap kelas</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Back to attendance index -->
                    <a href="{{ route('admin.attendance.index') }}"
                        class="btn btn-sm bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 hover:border-slate-300 normal-case rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Data Presensi
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('admin.attendance.recap') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Start Date -->
                <div class="form-control w-full space-y-2">
                    <label for="start_date" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Dari Tanggal</label>
                    <input type="text" id="start_date" name="start_date" value="{{ $filters['start_date'] }}" placeholder="Pilih Tanggal..."
                        class="datepicker input input-bordered input-sm w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                </div>

                <!-- End Date -->
                <div class="form-control w-full space-y-2">
                    <label for="end_date" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Sampai Tanggal</label>
                    <input type="text" id="end_date" name="end_date" value="{{ $filters['end_date'] }}" placeholder="Pilih Tanggal..."
                        class="datepicker input input-bordered input-sm w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
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

    <!-- Recap Table Card -->
    <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/60 text-slate-500 font-bold text-[11px] uppercase tracking-wider">
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Jurusan</th>
                        <th class="px-6 py-4 text-center">Hadir</th>
                        <th class="px-6 py-4 text-center">Telat</th>
                        <th class="px-6 py-4 text-center">Sakit</th>
                        <th class="px-6 py-4 text-center">Izin</th>
                        <th class="px-6 py-4 text-center">Alpha</th>
                        <th class="px-6 py-4 text-center">Total</th>
                        <th class="px-6 py-4">Persentase Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="font-semibold text-sm text-neutral-700 divide-y divide-slate-100">
                    @forelse($recap as $row)
                        <tr class="hover:bg-slate-50/45 transition-colors">
                            <!-- Kelas -->
                            <td class="px-6 py-4">
                                <span class="block text-neutral-800 font-bold leading-tight">{{ $row['class_name'] }}</span>
                            </td>

                            <!-- Jurusan -->
                            <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                {{ $row['department'] }}
                            </td>

                            <!-- Hadir -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $row['hadir'] }}</span>
                            </td>

                            <!-- Telat -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-100">{{ $row['telat'] }}</span>
                            </td>

                            <!-- Sakit -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">{{ $row['sakit'] }}</span>
                            </td>

                            <!-- Izin -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">{{ $row['izin'] }}</span>
                            </td>

                            <!-- Alpha -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-100">{{ $row['alpha'] }}</span>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 text-center text-xs font-bold text-slate-700">
                                {{ $row['total'] }}
                            </td>

                            <!-- Persentase Kehadiran -->
                            <td class="px-6 py-4 min-w-[180px]">
                                @php
                                    $rate = $row['attendance_rate'];
                                    if ($rate >= 85) {
                                        $barColor = 'bg-emerald-500';
                                        $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                    } elseif ($rate >= 70) {
                                        $barColor = 'bg-amber-500';
                                        $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                    } else {
                                        $barColor = 'bg-rose-500';
                                        $badgeColor = 'bg-rose-50 text-rose-700 border-rose-100';
                                    }
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeColor }} border whitespace-nowrap">
                                        {{ number_format($rate, 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-slate-400 font-bold">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-50 text-slate-300 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400 tracking-wide">Tidak ada data presensi pada rentang tanggal ini</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
