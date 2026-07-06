<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Modern Dashboard')</title>

  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- We keep DaisyUI for other pages compatibility, but it will not interfere with our custom classes below -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Flatpickr for Modern Datepicker -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

  <style>
    *{
      font-family: 'Poppins', sans-serif;
    }

    body{
      background: #f4f7fe;
    }

    /* Override any framework card class with the custom requested design */
    .card {
      background: white;
      border-radius: 24px !important;
      box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important;
      display: block !important;
    }

    .sidebar-item{
      transition: .3s;
    }

    .sidebar-item:hover{
      background: #edf2ff;
      transform: translateX(5px);
    }

    .active-menu{
      background: linear-gradient(135deg,#4f46e5,#2563eb);
      color: white;
      box-shadow: 0 10px 20px rgba(79,70,229,.25);
    }

    .gradient-card{
      background: linear-gradient(135deg,#4f46e5,#2563eb);
    }
  </style>
  
  @yield('styles')
</head>

<body>

<div class="min-h-screen">

  <!-- SIDEBAR -->
  <aside class="w-[280px] bg-white border-r border-slate-200 p-6 hidden lg:flex flex-col justify-between fixed h-screen overflow-y-auto z-40">

    <div>

      <!-- LOGO -->
      <div class="flex items-center gap-4 mb-12">

        <div class="w-14 h-14 rounded-2xl gradient-card flex items-center justify-center text-white font-bold text-2xl">
          D
        </div>

        <div>
          <h1 class="text-2xl font-bold text-slate-800">
            Dashboard
          </h1>

          <p class="text-slate-400 text-sm">
            Smart Admin Panel
          </p>
        </div>

      </div>

      <!-- MENU -->
      <div class="space-y-3">
        @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10m-9 11h4" />
          </svg>
          Dashboard
        </a>

        <a href="{{ route('admin.attendance.index') }}" class="sidebar-item {{ request()->routeIs('admin.attendance.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V9a1 1 0 011-1h1a1 1 0 011 1v10m-3 0H7a1 1 0 01-1-1v-6a1 1 0 011-1h1m3 8h2m0 0h1a1 1 0 001-1V7a1 1 0 00-1-1h-1a1 1 0 00-1 1v11z" />
          </svg>
          Statistik
        </a>

        <a href="{{ route('admin.students.index') }}" class="sidebar-item {{ request()->routeIs('admin.students.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m5-3.13a3 3 0 100-6 3 3 0 000 6zm6 1a3 3 0 10-2.83-4M7 10a3 3 0 102.83-4" />
          </svg>
          Pengguna
        </a>

        <a href="{{ route('admin.face-data.index') }}" class="sidebar-item {{ request()->routeIs('admin.face-data.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
          </svg>
          Galeri Wajah
        </a>

        <a href="{{ route('admin.logs') }}" class="sidebar-item {{ request()->routeIs('admin.logs') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
          Log Aktivitas
        </a>

        <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Pengaturan
        </a>
        @endif
      </div>

    </div>

    <!-- SERVER -->
    <div>
        <div class="card p-5 mb-4">

          <p class="text-slate-400 text-sm mb-2">
            STATUS SERVER
          </p>

          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-slate-700">
              Online
            </h3>

            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></div>
          </div>

        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full sidebar-item flex items-center justify-center gap-3 px-5 py-3 rounded-2xl font-medium text-red-500 hover:text-red-600 bg-red-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Sistem
            </button>
        </form>
    </div>

  </aside>

  <!-- MAIN -->
  <main class="flex-1 p-8 lg:ml-[280px] min-w-0">

    <!-- TOPBAR -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-10">

      <div>
        <p class="text-slate-400 mb-2">
          Dashboard / @yield('page_title', 'Overview')
        </p>

        <h1 class="text-4xl font-bold text-slate-800">
          @yield('header_title', 'Selamat Datang')
        </h1>

        <p class="text-slate-500 mt-2">
          @yield('header_subtitle', 'Monitor statistik dan aktivitas sistem secara real-time.')
        </p>
      </div>

      <!-- SEARCH -->
      <div class="flex items-center gap-4">

        <div class="bg-white px-5 py-4 rounded-2xl shadow-sm flex items-center gap-3 flex-1 lg:flex-none lg:w-[320px]">

          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>

          <input 
            type="text"
            placeholder="Cari sesuatu..."
            class="outline-none w-full bg-transparent"
          >

        </div>

        <a href="/" target="_blank" title="Buka Kios Presensi" class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-500 shadow-sm hover:shadow-md hover:text-primary transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
          </svg>
        </a>

        <div class="w-14 h-14 rounded-2xl gradient-card flex items-center justify-center text-white font-bold text-lg shadow-lg">
          {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>

      </div>

    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl font-medium flex items-center gap-3 shadow-sm border border-green-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl font-medium flex items-center gap-3 shadow-sm border border-red-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')

  </main>

</div>

@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr on all elements with .datepicker class
        flatpickr(".datepicker", {
            locale: "id", // Indonesian locale
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            allowInput: true,
            // Custom styling to match our design
            disableMobile: "true"
        });
    });
</script>
</body>
</html>
