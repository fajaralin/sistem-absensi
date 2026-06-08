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

<div class="flex min-h-screen">

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
          <span>🏠</span>
          Dashboard
        </a>

        <a href="{{ route('admin.attendance.index') }}" class="sidebar-item {{ request()->routeIs('admin.attendance.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <span>📊</span>
          Statistik
        </a>

        <a href="{{ route('admin.students.index') }}" class="sidebar-item {{ request()->routeIs('admin.students.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <span>👥</span>
          Pengguna
        </a>

        <a href="{{ route('admin.face-data.index') }}" class="sidebar-item {{ request()->routeIs('admin.face-data.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <span>🗂️</span>
          Galeri Wajah
        </a>

        <a href="{{ route('admin.logs') }}" class="sidebar-item {{ request()->routeIs('admin.logs') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <span>📋</span>
          Log Aktivitas
        </a>

        <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active-menu' : 'text-slate-700' }} w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-medium">
          <span>⚙️</span>
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
                <span>🚪</span> Keluar Sistem
            </button>
        </form>
    </div>

  </aside>

  <!-- MAIN -->
  <main class="flex-1 p-8 lg:ml-[280px]">

    <!-- TOPBAR -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-10">

      <div>
        <p class="text-slate-400 mb-2">
          Dashboard / @yield('page_title', 'Overview')
        </p>

        <h1 class="text-4xl font-bold text-slate-800">
          @yield('header_title', 'Selamat Datang 👋')
        </h1>

        <p class="text-slate-500 mt-2">
          @yield('header_subtitle', 'Monitor statistik dan aktivitas sistem secara real-time.')
        </p>
      </div>

      <!-- SEARCH -->
      <div class="flex items-center gap-4">

        <div class="bg-white px-5 py-4 rounded-2xl shadow-sm flex items-center gap-3 w-full lg:w-[320px]">

          <span class="text-slate-400">🔍</span>

          <input 
            type="text"
            placeholder="Cari sesuatu..."
            class="outline-none w-full bg-transparent"
          >

        </div>

        <a href="/" target="_blank" title="Buka Kios Presensi" class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm hover:shadow-md transition">
          🖥️
        </a>

        <div class="w-14 h-14 rounded-2xl gradient-card flex items-center justify-center text-white font-bold text-lg shadow-lg">
          {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>

      </div>

    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl font-medium flex items-center gap-3 shadow-sm border border-green-200">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl font-medium flex items-center gap-3 shadow-sm border border-red-200">
            <span>❌</span> {{ session('error') }}
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
