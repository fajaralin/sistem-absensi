@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Overview')
@section('header_title', 'Selamat Datang 👋')
@section('header_subtitle', 'Monitor statistik dan aktivitas sistem secara real-time.')

@section('content')
    <!-- STATISTIC -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

      <!-- CARD 1: Purple Gradient -->
      <div class="rounded-[24px] p-6 text-white shadow-xl bg-gradient-to-br from-violet-500 to-purple-600">

        <div class="flex justify-between items-start mb-5">
          <div>
            <p class="text-white/80 text-sm">
              Hadir Hari Ini
            </p>
            <h2 class="text-4xl font-bold mt-3 text-white">
              {{ $stats['hadir'] }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl backdrop-blur-sm">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
          </div>

        </div>

        <div class="flex items-center gap-2">
          <span class="text-white font-semibold">
            + Face ID
          </span>
          <span class="text-white/70 text-sm">
            Verified
          </span>
        </div>

      </div>

      <!-- CARD 2: Teal/Cyan Gradient -->
      <div class="rounded-[24px] p-6 text-white shadow-xl bg-gradient-to-br from-cyan-500 to-blue-600">

        <div class="flex justify-between items-start mb-5">
          <div>
            <p class="text-white/80 text-sm">
              Total Terdaftar
            </p>
            <h2 class="text-4xl font-bold mt-3 text-white">
              {{ $totalStudents }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl backdrop-blur-sm">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
          </div>

        </div>

        <div class="flex items-center gap-2">
          <span class="text-white font-semibold">
            {{ $activeStudentsCount }}
          </span>
          <span class="text-white/70 text-sm">
            Siswa Aktif
          </span>
        </div>

      </div>

      <!-- CARD 3: Pink/Rose Gradient -->
      <div class="rounded-[24px] p-6 text-white shadow-xl bg-gradient-to-br from-pink-500 to-rose-500">

        <div class="flex justify-between items-start mb-5">
          <div>
            <p class="text-white/80 text-sm">
              Rasio Kehadiran
            </p>
            <h2 class="text-4xl font-bold mt-3 text-white">
              {{ number_format($attendancePercentage, 1) }}%
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl backdrop-blur-sm">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
          </div>

        </div>

        <div class="flex items-center gap-2">
          <span class="text-white/70 text-sm">
            Sakit: {{ $stats['sakit'] }}, Izin: {{ $stats['izin'] }}
          </span>
        </div>

      </div>

      <!-- CARD 4 -->
      <div class="gradient-card rounded-[24px] p-6 text-white shadow-xl">

        <p class="opacity-80">
          Performa Server
        </p>

        <h2 class="text-5xl font-bold mt-4">
          99%
        </h2>

        <div class="mt-6 bg-white/20 rounded-full h-3 overflow-hidden">
          <div class="bg-white h-full w-[99%] rounded-full"></div>
        </div>

      </div>

    </section>

    <!-- CONTENT -->
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">

      <!-- TABLE -->
      <div class="xl:col-span-2 card p-6">

        <div class="flex justify-between items-center mb-6">

          <h2 class="text-2xl font-bold text-slate-800">
            Data Presensi Hari Ini
          </h2>

          <a href="{{ route('admin.attendance.index') }}" class="gradient-card text-white px-5 py-3 rounded-2xl shadow-lg hover:scale-105 transition">
            Lihat Semua
          </a>

        </div>

        <div class="overflow-x-auto">

          <table class="w-full">

            <thead>
              <tr class="text-slate-400 text-left border-b">
                <th class="pb-4">Nama Siswa</th>
                <th class="pb-4">Kelas</th>
                <th class="pb-4">Jam Masuk</th>
                <th class="pb-4">Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse($todayPresences as $pres)
              <tr class="border-b last:border-none">

                <td class="py-5">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center font-bold text-indigo-700">
                      {{ substr($pres->student->user->name, 0, 1) }}
                    </div>

                    <div>
                      <h3 class="font-semibold text-slate-700">
                        {{ $pres->student->user->name }}
                      </h3>
                      <p class="text-sm text-slate-400">
                        NISN: {{ $pres->student->nisn }}
                      </p>
                    </div>
                  </div>
                </td>

                <td>
                  <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl text-sm font-medium">
                    {{ $pres->student->class_name }}
                  </span>
                </td>

                <td>
                  <span class="text-green-500 font-semibold">
                    {{ \Carbon\Carbon::parse($pres->check_in)->format('H:i') }} WIB
                  </span>
                </td>

                <td>
                  <a href="{{ route('admin.attendance.index', ['search' => $pres->student->nisn]) }}" class="inline-block border border-indigo-500 text-indigo-600 px-5 py-2 rounded-xl hover:bg-indigo-50 transition">
                    Detail
                  </a>
                </td>

              </tr>
              @empty
              <tr>
                  <td colspan="4" class="py-10 text-center text-slate-400">
                      Belum ada presensi yang masuk hari ini.
                  </td>
              </tr>
              @endforelse

            </tbody>

          </table>

        </div>

      </div>

      <!-- ACTIVITY -->
      <div class="card p-6">

        <h2 class="text-2xl font-bold text-slate-800 mb-6">
          Aktivitas
        </h2>

        <div class="space-y-5 overflow-y-auto max-h-[400px] pr-2">
            @forelse($recentLogs as $log)
                @php
                    $isSuccess = str_contains(strtolower($log->action), 'success') || str_contains(strtolower($log->action), 'login');
                    $isFail = str_contains(strtolower($log->action), 'fail') || str_contains(strtolower($log->action), 'error') || str_contains(strtolower($log->action), 'delete');
                    $bgColor = $isSuccess ? 'bg-green-100' : ($isFail ? 'bg-red-100' : 'bg-blue-100');
                    $textColor = $isSuccess ? 'text-green-600' : ($isFail ? 'text-red-600' : 'text-blue-600');
                    $icon = $isSuccess ? '✔' : ($isFail ? '✖' : 'ℹ');
                @endphp
          <div class="flex items-center gap-4">

            <div class="w-14 h-14 shrink-0 rounded-2xl {{ $bgColor }} flex items-center justify-center {{ $textColor }} text-xl">
              {{ $icon }}
            </div>

            <div class="overflow-hidden">
              <h3 class="font-semibold text-slate-700 capitalize truncate" title="{{ str_replace('_', ' ', $log->action) }}">
                {{ str_replace('_', ' ', $log->action) }}
              </h3>

              <p class="text-slate-400 text-sm">
                {{ $log->created_at->diffForHumans() }}
              </p>
            </div>

          </div>
          @empty
              <p class="text-slate-400">Belum ada aktivitas.</p>
          @endforelse

        </div>

      </div>

    </section>

@endsection
