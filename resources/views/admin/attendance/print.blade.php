<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi - {{ $dateLabel }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN (For print layout styling simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .print-break-inside-avoid {
                page-break-inside: avoid;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <!-- Top Navigation Actions (No Print) -->
    <div class="no-print mb-8 flex justify-between items-center bg-slate-50 border border-slate-200 p-4 rounded-2xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.attendance.index', ['date' => $filters['date'] ?? today()->toDateString()]) }}" class="flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Rekap
            </a>
            <span class="text-slate-300">|</span>
            <p class="text-xs text-slate-500 font-medium">Tips: Simpan sebagai PDF melalui dialog cetak browser Anda (Ctrl+P / Cmd+P).</p>
        </div>
        <button onclick="window.print()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-indigo-100 transition-all cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Laporan / Simpan PDF
        </button>
    </div>

    <!-- Kop Surat Akademik -->
    <div class="border-b-4 border-slate-900 pb-5 mb-8 flex items-center justify-between">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl shadow-lg">
                🏫
            </div>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 uppercase">SMA NEGERI 1 UTAMA</h1>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-0.5">Bidang Kurikulum & Pembinaan Kesiswaan &bull; Tata Usaha Akademik</p>
                <p class="text-xs text-slate-400 mt-0.5">Jl. Raya Utama No. 123, Jakarta Selatan | Telp: (021) 555-1234 &bull; Email: info@sman1utama.sch.id</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 border border-slate-200 rounded-full uppercase tracking-wider text-slate-600">Dokumen Kurikulum Resmi</span>
            <p class="text-xs text-slate-400 mt-2">Dibuat: {{ now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>

    <!-- Laporan Title & Metadata -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-900 text-center uppercase tracking-wide">LAPORAN REKAPITULASI KEHADIRAN SISWA</h2>
        <h3 class="text-md font-semibold text-slate-500 text-center mt-1">Periode Tanggal: {{ $dateLabel }}</h3>

        <div class="grid grid-cols-3 gap-4 mt-6 text-sm bg-slate-50 p-4 border border-slate-200/80 rounded-2xl">
            <div>
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-xs">Total Roster Presensi</p>
                <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $attendances->count() }} Data Kehadiran</p>
            </div>
            <div>
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-xs">Filter Status</p>
                <p class="text-lg font-bold text-slate-800 mt-0.5 capitalize">{{ $filters['status'] ?? 'Semua Status (Hadir/Sakit/Izin/Alpha)' }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-xs">Pencarian NISN/Nama</p>
                <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $filters['search'] ?? 'Tidak Ada Filter Pencarian' }}</p>
            </div>
        </div>
    </div>

    <!-- Main Attendance Table -->
    <div class="overflow-hidden border border-slate-300 rounded-2xl shadow-sm mb-12">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-300 font-bold uppercase tracking-wider text-xs">
                    <th class="py-4 px-4 border-r border-slate-200 text-center w-12">No</th>
                    <th class="py-4 px-4 border-r border-slate-200">NISN</th>
                    <th class="py-4 px-4 border-r border-slate-200">Nama Lengkap</th>
                    <th class="py-4 px-4 border-r border-slate-200">Jurusan / Kelas</th>
                    <th class="py-4 px-4 border-r border-slate-200 text-center">Tanggal</th>
                    <th class="py-4 px-4 border-r border-slate-200 text-center">Jam Scan</th>
                    <th class="py-4 px-4 border-r border-slate-200 text-center">Status</th>
                    <th class="py-4 px-4 border-r border-slate-200 text-center">Metode</th>
                    <th class="py-4 px-4 text-center">Confidence AI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-slate-800">
                @forelse($attendances as $idx => $att)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 border-r border-slate-200 text-center font-bold text-slate-500">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 border-r border-slate-200 font-mono font-bold text-slate-700">{{ $att->student->nisn }}</td>
                        <td class="py-3 px-4 border-r border-slate-200 font-bold text-slate-900">{{ $att->student->user->name }}</td>
                        <td class="py-3 px-4 border-r border-slate-200 text-xs">
                            <span class="font-semibold text-slate-700 block">{{ $att->student->department }}</span>
                            <span class="text-slate-400 font-medium block">Kelas: {{ $att->student->class_name }}</span>
                        </td>
                        <td class="py-3 px-4 border-r border-slate-200 text-center font-medium text-slate-600">
                            {{ $att->date->translatedFormat('d-m-Y') }}
                        </td>
                        <td class="py-3 px-4 border-r border-slate-200 text-center font-mono font-bold text-slate-700">
                            {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '-' }}
                        </td>
                        <td class="py-3 px-4 border-r border-slate-200 text-center">
                            @if($att->status === 'hadir')
                                <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">HADIR</span>
                            @elseif($att->status === 'sakit')
                                <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded-full bg-amber-100 text-amber-800 border border-amber-200">SAKIT</span>
                            @elseif($att->status === 'izin')
                                <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded-full bg-blue-100 text-blue-800 border border-blue-200">IZIN</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded-full bg-rose-100 text-rose-800 border border-rose-200">ALPHA</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-r border-slate-200 text-center text-xs font-semibold text-slate-600">
                            {{ $att->method === 'face_recognition' ? 'Scan Wajah' : 'Manual Admin' }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold">
                            @if($att->confidence)
                                <span class="text-indigo-600">{{ number_format($att->confidence * 100, 1) }}%</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 px-4 text-center font-bold text-slate-400 text-base">
                            Tidak ada data presensi yang sesuai dengan kriteria laporan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Signatures Section -->
    <div class="print-break-inside-avoid grid grid-cols-2 gap-12 mt-16 text-sm font-semibold text-slate-800">
        <div class="text-center">
            <p class="text-slate-400 uppercase tracking-wider text-xs font-bold">Mengetahui,</p>
            <p class="mt-1 font-bold text-slate-900">Kepala Sekolah SMA Negeri 1 Utama</p>
            <div class="h-24"></div>
            <p class="font-extrabold text-slate-900 border-b border-slate-400 inline-block px-8 pb-1">Drs. H. Mulyadi, M.Pd.</p>
            <p class="text-xs text-slate-400 mt-1 font-mono">NIP. 19680515 199303 1 003</p>
        </div>
        <div class="text-center">
            <p class="text-slate-400 uppercase tracking-wider text-xs font-bold">Petugas Administrasi,</p>
            <p class="mt-1 font-bold text-slate-900">Tata Usaha / Administrasi Kesiswaan</p>
            <div class="h-24"></div>
            <p class="font-extrabold text-slate-900 border-b border-slate-400 inline-block px-8 pb-1">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-400 mt-1 font-mono">NIP / NUP. {{ auth()->user()->id + 9920310 }}</p>
        </div>
    </div>

    <!-- Printing Auto-Trigger -->
    <script>
        // Auto trigger window print only when loaded with a auto-print query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('autoprint')) {
            window.onload = function() {
                setTimeout(() => { window.print(); }, 500);
            }
        }
    </script>
</body>
</html>
