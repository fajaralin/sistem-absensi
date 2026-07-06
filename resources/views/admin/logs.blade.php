@extends('layouts.app')

@section('title', 'Audit System Logs')
@section('page_title', 'System Logs')

@section('content')
<div class="space-y-8">
    <!-- Header Page Description -->
    <div class="card bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="p-3 bg-primary/10 rounded-2xl text-primary mt-0.5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-neutral-800 tracking-tight">
                        Log Audit & Aktivitas Sistem
                    </h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">
                        Catatan riwayat aktivitas operasional sistem secara real-time untuk keperluan audit keamanan, performa rekognisi AI, dan debugging presentasi UAS.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-2xl text-xs font-bold shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-450 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Monitoring Live
                </span>
            </div>
        </div>
    </div>

    <!-- Logs Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Logs -->
        <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl p-6 flex flex-row items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Aktivitas</span>
                <span class="text-lg font-bold text-neutral-800 mt-0.5 block">{{ $logs->count() }} Entri Log</span>
            </div>
        </div>

        <!-- Success Logs -->
        <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl p-6 flex flex-row items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sukses Rekognisi AI</span>
                <span class="text-lg font-bold text-emerald-600 mt-0.5 block">
                    {{ $logs->filter(fn($l) => str_contains($l->action, 'SUCCESS') || str_contains($l->action, 'CREATED') || str_contains($l->action, 'UPDATED'))->count() }} Entri
                </span>
            </div>
        </div>

        <!-- Failed/Warning Logs -->
        <div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl p-6 flex flex-row items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Gagal Scan / Error</span>
                <span class="text-lg font-bold text-rose-600 mt-0.5 block">
                    {{ $logs->filter(fn($l) => str_contains($l->action, 'FAILED') || str_contains($l->action, 'ERROR') || str_contains($l->action, 'DELETED'))->count() }} Entri
                </span>
            </div>
        </div>
    </div>

    <!-- Main Audit Logs Table Card -->
    <div class="card bg-white border border-slate-200/60 rounded-3xl overflow-hidden shadow-sm p-0">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h4 class="font-bold text-neutral-800 text-sm">Riwayat Aktivitas Terbaru (Max 100 Entri)</h4>
            <span class="text-xs font-semibold text-slate-400">Diurutkan berdasarkan waktu terbaru</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-sm w-full text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/60 text-slate-500 font-bold text-[11px] uppercase tracking-wider">
                        <th class="py-4 px-6 w-12 text-center">ID</th>
                        <th class="py-4 px-6 w-48">Waktu Kejadian</th>
                        <th class="py-4 px-6 w-44">Tipe Aksi</th>
                        <th class="py-4 px-6 w-44">Pelaku (User)</th>
                        <th class="py-4 px-6">Detail Deskripsi Aktivitas</th>
                        <th class="py-4 px-6 w-36 text-center">IP Address</th>
                        <th class="py-4 px-6 w-52">Device / User Agent</th>
                    </tr>
                </thead>
                <tbody class="font-semibold text-xs text-neutral-700 divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/45 transition-colors">
                            <!-- Log ID -->
                            <td class="py-3 px-6 text-center font-bold text-slate-450">#{{ $log->id }}</td>
                            
                            <!-- Timestamp -->
                            <td class="py-3 px-6 leading-tight text-slate-600 font-bold">
                                {{ $log->created_at->translatedFormat('d M Y') }}
                                <span class="block text-[10px] text-slate-400 font-mono mt-0.5 font-semibold">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            
                            <!-- Action Type (Color Coded Badges) -->
                            <td class="py-3 px-6">
                                @if(str_contains($log->action, 'SUCCESS') || str_contains($log->action, 'VERIFIED'))
                                    <span class="inline-flex justify-center items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100/60 w-full text-center">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                @elseif(str_contains($log->action, 'FAILED') || str_contains($log->action, 'ERROR'))
                                    <span class="inline-flex justify-center items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-100/60 w-full text-center">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                @elseif(str_contains($log->action, 'CREATED') || str_contains($log->action, 'REGISTERED'))
                                    <span class="inline-flex justify-center items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100/60 w-full text-center">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                @elseif(str_contains($log->action, 'DELETED'))
                                    <span class="inline-flex justify-center items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200/60 w-full text-center">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                @else
                                    <span class="inline-flex justify-center items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100/60 w-full text-center">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Actor (User) -->
                            <td class="py-3 px-6 leading-tight">
                                @if($log->user)
                                    <span class="block text-neutral-800 font-bold">{{ $log->user->name }}</span>
                                    <span class="block text-[9px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Role: {{ $log->user->role }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-primary/5 text-primary border border-primary/10 shadow-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 00-2 2zM9 9h6v6H9V9z" />
                                        </svg>
                                        Sistem Otomatis
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Details Description -->
                            <td class="py-3 px-6 text-slate-650 font-semibold leading-relaxed max-w-sm lg:max-w-md">
                                {{ $log->details }}
                            </td>
                            
                            <!-- IP Address -->
                            <td class="py-3 px-6 text-center font-mono font-bold text-slate-500">
                                {{ $log->ip_address }}
                            </td>
                            
                            <!-- User Agent / Device Details -->
                            <td class="py-3 px-6 font-semibold text-slate-400 truncate max-w-xs" title="{{ $log->user_agent }}">
                                @php
                                    $ua = $log->user_agent;
                                    $device = 'Unknown OS';
                                    if (str_contains($ua, 'Windows')) $device = 'Windows PC';
                                    elseif (str_contains($ua, 'Macintosh')) $device = 'Apple Mac';
                                    elseif (str_contains($ua, 'iPhone')) $device = 'iPhone';
                                    elseif (str_contains($ua, 'Android')) $device = 'Android Phone';
                                    elseif (str_contains($ua, 'Linux')) $device = 'Linux OS';

                                    $browser = 'Unknown Browser';
                                    if (str_contains($ua, 'Chrome')) $browser = 'Chrome';
                                    elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) $browser = 'Safari';
                                    elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
                                    elseif (str_contains($ua, 'Edg')) $browser = 'Edge';
                                @endphp
                                <span class="text-neutral-700 font-bold block">{{ $device }}</span>
                                <span class="text-[10px] block mt-0.5 font-bold text-slate-400">{{ $browser }} &bull; {{ Str::limit($ua, 40) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400 font-bold">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-50 text-slate-350 rounded-full border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5m16 0L13 16l-1 2.5L10 16l-3-2.5" />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400 tracking-wide uppercase">Belum ada aktivitas sistem yang tercatat di database logs.</span>
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

