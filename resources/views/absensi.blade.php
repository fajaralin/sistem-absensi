<!DOCTYPE html>
<html lang="id" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Presensi Biometrik - SMAN 1 Utama</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- DaisyUI and Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(rgba(255, 255, 255, 0.72), rgba(255, 255, 255, 0.76)), url('{{ asset('images/school_bg.png') }}') no-repeat center center fixed;
            background-size: cover;
        }
        
        .scan-line {
            animation: scan 2.5s linear infinite;
        }
        
        @keyframes scan {
            0% { top: 0%; opacity: 0.3; }
            50% { top: 100%; opacity: 1; }
            100% { top: 0%; opacity: 0.3; }
        }

        .biometric-glow {
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="text-base-content min-h-screen flex flex-col antialiased selection:bg-primary selection:text-primary-content"
    x-data="absensiKiosk()" x-init="initKiosk()">

    <!-- Futuristic Header -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-base-200 sticky top-0 z-30 px-6 py-4 shrink-0 transition-all duration-300">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-4">
                <!-- School Logo SVG -->
                <div class="w-12 h-12 bg-gradient-to-tr from-primary to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-center sm:text-left">
                    <h1 class="text-md font-bold tracking-wide text-base-content leading-tight uppercase">SMA NEGERI 1 UTAMA</h1>
                    <p class="text-[9px] font-bold text-primary tracking-widest uppercase mt-0.5">Sistem Presensi Biometrik Lobi Sekolah</p>
                </div>
            </div>
            
            <!-- Clock and Links -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <div class="text-xs font-semibold text-base-content/60" x-text="currentDate"></div>
                    <div class="text-[9px] text-secondary font-bold tracking-wider uppercase mt-0.5">Sesi Kehadiran Harian</div>
                </div>
                
                <div class="bg-slate-50 border border-base-200/80 px-4 py-2 rounded-xl shadow-sm">
                    <div class="text-lg font-mono font-bold tracking-widest text-primary" x-text="currentTime"></div>
                </div>
                
                <a href="/login" class="btn btn-sm btn-outline border-base-200 hover:btn-primary gap-2 rounded-xl text-xs font-bold shadow-sm transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Admin Portal
                </a>
            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 md:p-8 flex flex-col justify-center items-center">
        
        <!-- STEP 1: Direct Scan (No NISN/Name input — system identifies the student automatically) -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="card w-full max-w-2xl bg-white/95 backdrop-blur-md border border-base-200 shadow-2xl relative biometric-glow overflow-hidden rounded-[2rem]">
            <div class="card-body p-6 md:p-10 space-y-6">

            <div class="text-center space-y-2">
                <template x-if="!isLeaveMode">
                    <div class="inline-flex p-4 bg-primary/10 rounded-2xl text-primary border border-primary/20 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2M3 17v2a2 2 0 002 2h2m10-16h2a2 2 0 012 2v2m-4 14h2a2 2 0 002-2v-2M9 11a3 3 0 106 0 3 3 0 00-6 0zm-3 7c0-1.657 2.686-3 6-3s6 1.343 6 3" />
                        </svg>
                    </div>
                </template>
                <template x-if="isLeaveMode">
                    <div class="inline-flex p-4 bg-warning/10 rounded-2xl text-warning border border-warning/20 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </template>
                <h2 class="text-xl font-bold tracking-tight text-base-content" x-text="isLeaveMode ? 'Verifikasi Pengajuan Izin' : 'Pindai Wajah Anda'"></h2>
                <p class="text-xs text-base-content/55 max-w-md mx-auto leading-relaxed font-semibold"
                   x-text="isLeaveMode ? 'Hadapkan wajah Anda ke kamera untuk verifikasi biometrik. Sistem akan membuka form data izin setelah Anda teridentifikasi.' : 'Tidak perlu mengisi nama atau NISN — cukup posisikan wajah Anda di depan kamera, sistem akan otomatis mengenali identitas Anda dan mencatat kehadiran.'"></p>

                <!-- Status GPS Lokasi -->
                <div x-show="locationEnabledSetting === 'yes'" class="mt-3 flex justify-center">
                    <template x-if="locationFetching">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100/60 animate-pulse shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Mendapatkan Koordinat GPS...
                        </span>
                    </template>
                    <template x-if="!locationFetching && !locationError && isOutsideRadius && !isLeaveMode">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-sm animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Di Luar Jangkauan Sekolah (Presensi Nonaktif). Klik tombol Sakit/Izin di bawah.
                        </span>
                    </template>
                    <template x-if="!locationFetching && !locationError && (!isOutsideRadius || isLeaveMode)">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100/60 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            GPS Aktif: <span class="font-mono" x-text="latitude.toFixed(6) + ', ' + longitude.toFixed(6)"></span>
                        </span>
                    </template>
                    <template x-if="!locationFetching && locationError">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100/60 cursor-pointer shadow-sm animate-bounce" @click="fetchLocation()">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                            GPS Error: <span x-text="locationError"></span> 🔄
                        </span>
                    </template>
                </div>
            </div>

            <!-- Webcam viewport -->
            <div class="w-full aspect-[4/3] bg-slate-900 rounded-2xl border border-base-200/80 overflow-hidden relative shadow-inner flex items-center justify-center">

                <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]"></video>

                <!-- Scan Overlay Screen -->
                <div class="absolute inset-0 rounded-2xl pointer-events-none flex items-center justify-center bg-black/10">
                    <!-- Animated Scanner Line -->
                    <div x-show="analyzing" class="absolute left-0 w-full h-1 bg-primary/80 scan-line shadow-[0_0_15px_#3b82f6]"></div>

                    <!-- Face target guide grid -->
                    <div class="w-60 h-60 border-2 border-dashed border-primary/40 rounded-full flex items-center justify-center relative">
                        <div class="w-6 h-6 border-t-2 border-l-2 border-primary absolute top-0 left-0"></div>
                        <div class="w-6 h-6 border-t-2 border-r-2 border-primary absolute top-0 right-0"></div>
                        <div class="w-6 h-6 border-b-2 border-l-2 border-primary absolute bottom-0 left-0"></div>
                        <div class="w-6 h-6 border-b-2 border-r-2 border-primary absolute bottom-0 right-0"></div>
                    </div>
                </div>

                <!-- Loader Spinner overlay -->
                <div x-show="analyzing" class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center gap-4 text-center p-6 transition-all duration-300">
                    <span class="loading loading-ring loading-lg text-primary"></span>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-neutral-800 tracking-tight uppercase" x-text="isLeaveMode ? 'Memverifikasi Identitas Izin...' : 'Mengidentifikasi Wajah Anda...'"></h4>
                        <p class="text-[9px] text-base-content/55 font-bold uppercase tracking-wider">LBPH Biometric AI Face Matcher (1-to-Many)</p>
                    </div>
                    <progress class="progress progress-primary w-56" value="100" max="100"></progress>
                </div>
            </div>

            <!-- Auto Scan Status Panel -->
            <div class="w-full flex flex-col sm:flex-row gap-4 items-center justify-between pt-2.5 border-t border-slate-100">
                <div class="flex flex-wrap gap-2">
                    <template x-if="!isLeaveMode">
                        <button @click="switchToLeaveMode()" class="btn btn-sm bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white border-0 text-xs font-extrabold rounded-xl px-5 py-2.5 flex items-center gap-2 shadow-md shadow-orange-500/10 hover:shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Ajukan Sakit / Izin Siswa
                        </button>
                    </template>
                    <template x-if="isLeaveMode">
                        <button @click="switchToAttendanceMode()" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 border-0 text-xs font-bold rounded-xl px-5 py-2.5 flex items-center gap-2 shadow-sm hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Batal & Kembali
                        </button>
                    </template>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 rounded-xl shadow-sm border transition-all duration-300"
                    :class="isLeaveMode 
                        ? 'bg-primary/10 border-primary/20 text-primary' 
                        : (scanCountdown > 0 
                            ? 'bg-amber-500/10 border-amber-500/20 text-amber-600' 
                            : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600')">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                            :class="isLeaveMode ? 'bg-primary' : (scanCountdown > 0 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5"
                            :class="isLeaveMode ? 'bg-primary' : (scanCountdown > 0 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                    </span>
                    <span class="text-xs font-bold tracking-wide"
                        x-text="isLeaveMode 
                            ? 'Mencari Wajah Pengaju Izin...' 
                            : (locationEnabledSetting === 'yes' && locationFetching 
                                ? 'Menunggu GPS Lokasi...' 
                                : (scanCountdown > 0 
                                    ? 'Pemindaian dimulai dalam ' + scanCountdown + 's...' 
                                    : 'Pindai Wajah Aktif...'))"></span>
                </div>
            </div>
            </div>
        </div>

        <!-- STEP 2: Leave Submission Form (After successful identification in Leave Mode) -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="card w-full max-w-2xl bg-white/95 backdrop-blur-md border border-base-200 shadow-2xl relative biometric-glow overflow-hidden rounded-[2rem]">
            <div class="card-body p-6 md:p-10 space-y-6">
                
                <div class="text-center space-y-2">
                    <div class="inline-flex p-4 bg-warning/15 rounded-2xl text-warning border border-warning/20 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-base-content">Lengkapi Formulir Izin</h2>
                    <p class="text-xs text-base-content/55 max-w-md mx-auto leading-relaxed font-semibold">
                        Identitas Anda berhasil diverifikasi. Silakan lengkapi jenis alasan ketidakhadiran Anda di bawah ini.
                    </p>
                </div>

                <div x-show="identifiedStudent" class="bg-slate-50 border border-slate-200/60 p-5 rounded-2xl space-y-3.5 text-xs text-slate-700">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Detail Siswa Pengaju</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-warning/10 text-warning border border-warning/20 font-bold text-[9px] uppercase">Terverifikasi Biometrik</span>
                    </div>
                    <div class="grid grid-cols-2 gap-y-2 gap-x-4">
                        <div>
                            <span class="block font-semibold text-slate-400">Nama Lengkap</span>
                            <span class="font-bold text-slate-800 text-sm" x-text="identifiedStudent ? identifiedStudent.name : ''"></span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-400">NISN</span>
                            <span class="font-bold font-mono text-slate-800 text-sm" x-text="identifiedStudent ? identifiedStudent.nisn : ''"></span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-400">Kelas / Rombel</span>
                            <span class="font-bold text-slate-800" x-text="identifiedStudent ? identifiedStudent.class_name : ''"></span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-400">Jurusan</span>
                            <span class="font-bold text-slate-800" x-text="identifiedStudent ? identifiedStudent.department : ''"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Inputs -->
                <form @submit.prevent="submitLeaveRequest" class="space-y-4">
                    <!-- Status / Tipe Izin -->
                    <div class="form-control w-full space-y-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Pilih Jenis Ketidakhadiran</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 group"
                                :class="leaveStatus === 'sakit' ? 'bg-amber-50/40 border-amber-500 ring-4 ring-amber-100/50' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60'">
                                <input type="radio" name="leaveStatus" value="sakit" x-model="leaveStatus" class="radio radio-warning shrink-0">
                                <div>
                                    <span class="block font-bold text-slate-800 text-xs transition-colors" :class="leaveStatus === 'sakit' ? 'text-amber-800' : 'text-slate-700'">Sakit 🤒</span>
                                    <span class="block text-[10px] text-slate-400 font-semibold mt-0.5" :class="leaveStatus === 'sakit' ? 'text-amber-700/60' : 'text-slate-400'">Siswa berobat / istirahat medis</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 group"
                                :class="leaveStatus === 'izin' ? 'bg-blue-50/30 border-blue-500 ring-4 ring-blue-50/50' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60'">
                                <input type="radio" name="leaveStatus" value="izin" x-model="leaveStatus" class="radio radio-primary shrink-0">
                                <div>
                                    <span class="block font-bold text-slate-800 text-xs transition-colors" :class="leaveStatus === 'izin' ? 'text-blue-800' : 'text-slate-700'">Izin ✉️</span>
                                    <span class="block text-[10px] text-slate-400 font-semibold mt-0.5" :class="leaveStatus === 'izin' ? 'text-blue-700/60' : 'text-slate-400'">Urusan keluarga / kepentingan penting</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Keterangan / Alasan -->
                    <div class="form-control w-full space-y-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Keterangan / Alasan Detail</label>
                        <textarea required x-model="leaveNotes" rows="3" placeholder="Contoh: Mengalami demam tinggi sejak semalam dan disarankan dokter istirahat."
                            class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-warning/20 focus:border-warning text-xs font-semibold text-slate-700"></textarea>
                    </div>

                    <!-- Upload Surat/Dokumen -->
                    <div class="form-control w-full space-y-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Unggah Surat Bukti (Opsional)</label>
                        <input type="file" id="leave_attachment" accept="image/*" @change="handleFileChange"
                            class="file-input file-input-bordered file-input-warning w-full rounded-2xl text-xs font-semibold bg-slate-50 border-slate-200">
                        <p class="text-[10px] text-slate-400 font-semibold">Format didukung: JPG, PNG, WEBP. Maksimal ukuran berkas 2MB.</p>
                    </div>

                    <!-- Error message within form -->
                    <div x-show="leaveErrorMsg" class="p-4 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl text-xs font-bold" x-text="leaveErrorMsg"></div>

                    <!-- Form Footer Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="switchToAttendanceMode()" :disabled="submittingLeave"
                            class="btn btn-sm bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl px-6 h-11 font-bold text-xs hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Batal
                        </button>
                        <button type="submit" :disabled="submittingLeave"
                            class="btn btn-sm bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 border-0 text-white rounded-xl px-8 h-11 font-bold text-xs shadow-md shadow-orange-500/10 hover:shadow-orange-500/20 flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <span x-show="submittingLeave" class="loading loading-spinner loading-xs text-white"></span>
                            Kirim Pengajuan Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- STEP 3: Feedback Overlay Pop-Up -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="card w-full max-w-md bg-white/95 backdrop-blur-md border border-base-200 shadow-2xl relative biometric-glow overflow-hidden rounded-[2rem]">
            <div class="card-body p-8 text-center space-y-6">
            
            <!-- Success Icon & Header (Hadir tepat waktu) -->
            <template x-if="presenceResult.success && (presenceResult.status !== 'telat' && presenceResult.status !== 'sakit' && presenceResult.status !== 'izin')">
                <div class="space-y-6">
                    <div class="inline-flex p-5 bg-emerald-50 text-success rounded-full border border-emerald-100 text-5xl animate-bounce shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-xl font-bold text-emerald-600 tracking-tight">Presensi Berhasil!</h2>
                        <p class="text-sm text-base-content/85 font-semibold" x-text="presenceResult.message"></p>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto bg-emerald-50/50 p-4 rounded-xl border border-emerald-100/50 text-xs">
                        <div class="text-left space-y-1">
                            <span class="text-[9px] font-bold text-emerald-700/60 uppercase tracking-wider">Akurasi Pencocokan</span>
                            <div class="font-mono font-bold text-primary text-base" x-text="formatConfidence(presenceResult.confidence)"></div>
                        </div>
                        <div class="text-left space-y-1 border-l border-emerald-100/60 pl-4">
                            <span class="text-[9px] font-bold text-emerald-700/60 uppercase tracking-wider">Tipe Detektor</span>
                            <div class="font-bold text-neutral-800 text-xs mt-0.5">Biometrik Wajah</div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Success but Late Icon & Header (Hadir TELAT) -->
            <template x-if="presenceResult.success && presenceResult.status === 'telat'">
                <div class="space-y-6">
                    <div class="inline-flex p-5 bg-amber-50 text-warning rounded-full border border-amber-100 text-5xl animate-bounce shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-xl font-bold text-amber-600 tracking-tight">Presensi Tercatat — Terlambat</h2>
                        <p class="text-sm text-base-content/85 font-semibold" x-text="presenceResult.message"></p>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto bg-amber-50/50 p-4 rounded-xl border border-amber-100/50 text-xs">
                        <div class="text-left space-y-1">
                            <span class="text-[9px] font-bold text-amber-700/60 uppercase tracking-wider">Akurasi Pencocokan</span>
                            <div class="font-mono font-bold text-primary text-base" x-text="formatConfidence(presenceResult.confidence)"></div>
                        </div>
                        <div class="text-left space-y-1 border-l border-amber-100/60 pl-4">
                            <span class="text-[9px] font-bold text-amber-700/60 uppercase tracking-wider">Tipe Detektor</span>
                            <div class="font-bold text-neutral-800 text-xs mt-0.5">Biometrik Wajah</div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Success Leave Request (Sakit / Izin) -->
            <template x-if="presenceResult.success && (presenceResult.status === 'sakit' || presenceResult.status === 'izin')">
                <div class="space-y-6">
                    <div class="inline-flex p-5 bg-blue-50 text-info rounded-full border border-blue-100 text-5xl animate-bounce shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-xl font-bold text-blue-600 tracking-tight" x-text="'Pengajuan ' + presenceResult.status.toUpperCase() + ' Berhasil!'"></h2>
                        <p class="text-sm text-base-content/85 font-semibold" x-text="presenceResult.message"></p>
                    </div>
                </div>
            </template>

            <!-- Failure Icon & Header -->
            <template x-if="!presenceResult.success">
                <div class="space-y-6">
                    <div class="inline-flex p-5 bg-rose-50 text-error rounded-full border border-rose-100 text-5xl animate-pulse shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-xl font-bold text-rose-600 tracking-tight">Verifikasi Gagal!</h2>
                        <p class="text-sm text-base-content/85 font-semibold" x-text="presenceResult.message"></p>
                    </div>
                    
                    <!-- Advice -->
                    <ul class="text-left max-w-sm mx-auto bg-rose-50/40 p-4 rounded-xl border border-rose-100/50 text-xs text-base-content/75 space-y-2 leading-relaxed font-semibold">
                        <li class="flex items-center gap-2"><span class="text-rose-500 font-bold">•</span> Lepaskan masker, topi, kacamata hitam</li>
                        <li class="flex items-center gap-2"><span class="text-rose-500 font-bold">•</span> Posisikan wajah tepat di hadapan kamera</li>
                        <li class="flex items-center gap-2"><span class="text-rose-500 font-bold">•</span> Pastikan pencahayaan cukup terang</li>
                    </ul>
                </div>
            </template>

            <!-- Timer Countdown -->
            <div class="pt-4 border-t border-base-200 text-xs font-semibold text-base-content/50 flex items-center justify-center gap-2">
                <span>Halaman akan direset dalam</span>
                <span class="text-primary font-mono font-bold text-sm bg-slate-100 px-2.5 py-1 rounded-lg border border-base-200/85" x-text="countdown + 's'"></span>
            </div>

            <!-- Force Reset Button -->
            <button @click="resetToStep1"
                class="btn btn-block btn-neutral rounded-xl font-bold text-xs text-white tracking-wide hover:scale-[1.01] active:scale-[0.99] transition-all duration-200">
                Kembali Ke Awal Sekarang
            </button>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full bg-white/50 border-t border-base-200 py-4 shrink-0 text-center text-[10px] font-bold text-base-content/40 tracking-wider uppercase">
        &copy; 2026 &bull; Bidang Kurikulum & Kesiswaan &bull; Sistem Presensi Kiosk Pintar SMAN 1 UTAMA
    </footer>

    <!-- Hidden canvas for camera captures -->
    <canvas id="canvas" class="hidden" width="640" height="480"></canvas>

    <!-- Main Javascript Logic -->
    <script>
        function absensiKiosk() {
            return {
                step: 1,

                // Camera states
                webcamStream: null,
                analyzing: false,
                autoScanTimer: null,
                isScanning: false,
                scanCountdown: 0,
                scanCountdownInterval: null,
                
                // Presence result feedback
                presenceResult: {
                    success: false,
                    message: '',
                    name: '',
                    status: '',
                    confidence: 0.0
                },
                
                // Timer and clock states
                currentTime: '',
                currentDate: '',
                countdown: 5,
                countdownInterval: null,

                // GPS / Geolocation States
                locationEnabledSetting: '{{ $settings['attendance_location_enabled'] ?? 'no' }}',
                latitude: 0,
                longitude: 0,
                locationError: null,
                locationFetching: true,
                targetLatitude: {{ $settings['attendance_latitude'] ?? 'null' }},
                targetLongitude: {{ $settings['attendance_longitude'] ?? 'null' }},
                targetRadius: {{ $settings['attendance_radius'] ?? 100 }},
                isOutsideRadius: false,

                // Leave Flow States
                isLeaveMode: false,
                identifiedStudent: null,
                leaveStatus: 'sakit',
                leaveNotes: '',
                leaveFile: null,
                leaveErrorMsg: '',
                submittingLeave: false,

                initKiosk() {
                    // Start digital clock
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    // Fetch GPS location if setting enabled
                    if (this.locationEnabledSetting === 'yes') {
                        this.fetchLocation();
                    } else {
                        this.locationFetching = false;
                    }

                    // Kamera langsung aktif begitu halaman terbuka
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toTimeString().split(' ')[0];
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    this.currentDate = now.toLocaleDateString('id-ID', options);
                },

                // GPS / Geolocation Manager
                fetchLocation() {
                    if (!navigator.geolocation) {
                        this.locationError = "GPS tidak didukung oleh browser ini.";
                        this.locationFetching = false;
                        return;
                    }

                    this.locationFetching = true;
                    this.locationError = null;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.latitude = position.coords.latitude;
                            this.longitude = position.coords.longitude;
                            this.locationFetching = false;
                            this.locationError = null;

                            // Hitung jarak ke sekolah secara lokal
                            if (this.locationEnabledSetting === 'yes' && this.targetLatitude && this.targetLongitude) {
                                const dist = this.calculateDistanceClient(this.latitude, this.longitude, this.targetLatitude, this.targetLongitude);
                                this.isOutsideRadius = dist > this.targetRadius;
                            }
                        },
                        (err) => {
                            this.locationFetching = false;
                            switch(err.code) {
                                case err.PERMISSION_DENIED:
                                    this.locationError = "Akses GPS ditolak oleh browser.";
                                    break;
                                case err.POSITION_UNAVAILABLE:
                                    this.locationError = "Informasi lokasi GPS tidak tersedia.";
                                    break;
                                case err.TIMEOUT:
                                    this.locationError = "Waktu permintaan lokasi GPS habis.";
                                    break;
                                default:
                                    this.locationError = "Gagal mendeteksi lokasi GPS.";
                            }
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                },

                // Formula Haversine sisi client
                calculateDistanceClient(lat1, lon1, lat2, lon2) {
                    const R = 6371000; // radius Bumi dalam meter
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.asin(Math.sqrt(a));
                    return R * c;
                },

                // Camera Stream management
                async startCamera() {
                    const video = document.getElementById('webcam');
                    if (!video) return;

                    try {
                        this.webcamStream = await navigator.mediaDevices.getUserMedia({
                            video: { 
                                width: { ideal: 640 }, 
                                height: { ideal: 480 },
                                facingMode: 'user'
                            },
                            audio: false
                        });
                        video.srcObject = this.webcamStream;
                        
                        // Jalankan pemindaian otomatis
                        this.startAutoScan();
                    } catch (err) {
                        console.error("Gagal membuka kamera:", err);
                        alert("Kamera webcam tidak terdeteksi atau tidak diizinkan oleh browser.");
                        this.resetToStep1();
                    }
                },

                stopCamera() {
                    this.stopAutoScan();
                    if (this.webcamStream) {
                        this.webcamStream.getTracks().forEach(track => track.stop());
                        this.webcamStream = null;
                    }
                    const video = document.getElementById('webcam');
                    if (video) video.srcObject = null;
                },

                // Auto-scan logic
                startAutoScan() {
                    this.stopAutoScan(); // Hindari multiple interval
                    
                    if (this.isLeaveMode) {
                        this.scanCountdown = 0;
                        this.autoScanTimer = setInterval(async () => {
                            if (this.step === 1 && !this.analyzing && this.webcamStream) {
                                await this.silentScan();
                            }
                        }, 1800);
                    } else {
                        this.scanCountdown = 3;
                        
                        this.scanCountdownInterval = setInterval(() => {
                            if (this.locationEnabledSetting === 'yes' && this.locationFetching) {
                                return; // Tunggu GPS selesai dideteksi
                            }
                            
                            if (this.scanCountdown > 0) {
                                this.scanCountdown--;
                            } else {
                                clearInterval(this.scanCountdownInterval);
                                this.scanCountdownInterval = null;
                                
                                // Jalankan scan pertama dan set interval reguler
                                if (this.step === 1 && !this.analyzing && this.webcamStream) {
                                    this.silentScan();
                                    this.autoScanTimer = setInterval(async () => {
                                        if (this.step === 1 && !this.analyzing && this.webcamStream) {
                                            await this.silentScan();
                                        }
                                    }, 1800);
                                }
                            }
                        }, 1000);
                    }
                },

                stopAutoScan() {
                    if (this.autoScanTimer) {
                        clearInterval(this.autoScanTimer);
                        this.autoScanTimer = null;
                    }
                    if (this.scanCountdownInterval) {
                        clearInterval(this.scanCountdownInterval);
                        this.scanCountdownInterval = null;
                    }
                },

                async silentScan() {
                    if (this.isScanning) return;
                    this.isScanning = true;

                    const scanMode = this.isLeaveMode;

                    // Validasi Geolocation di Client jika diwajibkan (Kecuali Mode Izin, karena bisa diajukan dari rumah)
                    if (this.locationEnabledSetting === 'yes' && !this.isLeaveMode) {
                        if (this.locationFetching) {
                            this.isScanning = false;
                            return; // Tunggu sensor GPS selesai mendeteksi koordinat
                        }
                        if (this.locationError || this.isOutsideRadius) {
                            this.isScanning = false;
                            return; // Jangan lakukan auto-scan presensi jika berada di luar radius atau GPS error
                        }
                    }

                    const video = document.getElementById('webcam');
                    const canvas = document.getElementById('canvas');
                    if (!video || !canvas) {
                        this.isScanning = false;
                        return;
                    }

                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageBase64 = canvas.toDataURL('image/jpeg', 0.85);

                    try {
                        const response = await fetch('/absensi/scan-auto', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                image: imageBase64,
                                latitude: this.latitude || null,
                                longitude: this.longitude || null,
                                purpose: this.isLeaveMode ? 'leave' : 'attendance'
                            })
                        });

                        const result = await response.json();

                        // Discard if mode changed while fetch was in flight
                        if (this.isLeaveMode !== scanMode) {
                            this.isScanning = false;
                            return;
                        }

                        // Jika wajah tidak terdeteksi di kamera, abaikan diam-diam dan lanjutkan loop
                        if (!result.success && result.message && result.message.toLowerCase().includes('tidak terdeteksi')) {
                            this.isScanning = false;
                            return;
                        }

                        // Jika wajah terdeteksi (baik sukses dikenali maupun tidak terdaftar)
                        this.analyzing = true; // Tampilkan efek loading sejenak agar transisi tampak alami
                        
                        setTimeout(() => {
                            this.analyzing = false;
                            this.isScanning = false;

                            if (this.isLeaveMode) {
                                if (result.success) {
                                    this.stopCamera();
                                    this.identifiedStudent = result.student;
                                    this.leaveStatus = 'sakit';
                                    this.leaveNotes = '';
                                    this.leaveFile = null;
                                    this.leaveErrorMsg = '';
                                    
                                    // Reset file input
                                    const fileInput = document.getElementById('leave_attachment');
                                    if (fileInput) fileInput.value = '';
                                    
                                    this.step = 2; // Beralih ke Form Pengisian Izin
                                } else {
                                    this.presenceResult = {
                                        success: false,
                                        message: result.message,
                                        name: '',
                                        status: '',
                                        confidence: 0.0
                                    };
                                    this.stopCamera();
                                    this.step = 3;
                                    this.speakFeedback(this.presenceResult);
                                    this.startCountdown();
                                }
                            } else {
                                this.presenceResult = {
                                    success: result.success,
                                    message: result.message,
                                    name: result.name || '',
                                    status: result.status || '',
                                    confidence: result.confidence || 0.0
                                };

                                this.stopCamera();
                                this.step = 3;
                                this.speakFeedback(this.presenceResult);
                                this.startCountdown();
                            }
                        }, 800);

                    } catch (error) {
                        console.error('Pemindaian otomatis gagal:', error);
                        this.isScanning = false;
                    }
                },

                // Mode Leave Switchers
                switchToLeaveMode() {
                    this.isLeaveMode = true;
                    this.resetToStep1();
                },

                switchToAttendanceMode() {
                    this.isLeaveMode = false;
                    this.resetToStep1();
                },

                // File Attachment Manager for Leaves
                handleFileChange(e) {
                    const files = e.target.files;
                    if (files.length > 0) {
                        this.leaveFile = files[0];
                    } else {
                        this.leaveFile = null;
                    }
                },

                // Leave form submit handler
                async submitLeaveRequest() {
                    if (!this.identifiedStudent) return;

                    this.submittingLeave = true;
                    this.leaveErrorMsg = '';

                    const formData = new FormData();
                    formData.append('student_id', this.identifiedStudent.id);
                    formData.append('status', this.leaveStatus);
                    formData.append('notes', this.leaveNotes);
                    if (this.leaveFile) {
                        formData.append('attachment', this.leaveFile);
                    }
                    if (this.latitude) formData.append('latitude', this.latitude);
                    if (this.longitude) formData.append('longitude', this.longitude);

                    try {
                        const response = await fetch('/absensi/izin', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.presenceResult = {
                                success: true,
                                message: `Pengajuan ${this.leaveStatus.toUpperCase()} berhasil disimpan untuk ${this.identifiedStudent.name}.`,
                                name: this.identifiedStudent.name,
                                status: this.leaveStatus,
                                confidence: this.identifiedStudent.confidence
                            };
                            this.step = 3; // Ke layar feedback sukses
                            this.speakFeedback(this.presenceResult);
                            this.startCountdown();
                        } else {
                            this.leaveErrorMsg = result.message || 'Gagal mengirim pengajuan izin.';
                        }
                    } catch (error) {
                        console.error('Submit leave failed:', error);
                        this.leaveErrorMsg = 'Koneksi terganggu. Gagal mengirim pengajuan izin ke server.';
                    } finally {
                        this.submittingLeave = false;
                    }
                },

                // Speaks aloud presence/leave status (TTS)
                speakFeedback(result) {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel(); // Cancel any ongoing voices
                        
                        let text = '';
                        if (result.success && (result.status === 'sakit' || result.status === 'izin')) {
                            text = `Pengajuan izin ${result.status} berhasil disimpan. Semoga lekas sembuh atau lancar urusannya, halo ${result.name}.`;
                        } else if (result.success && result.status === 'telat') {
                            text = `Presensi tercatat, namun Anda terlambat, halo ${result.name}. Mohon datang lebih awal lain kali ya.`;
                        } else if (result.success) {
                            text = `Presensi berhasil. Halo ${result.name}. Selamat belajar!`;
                        } else {
                            text = result.message || "Wajah tidak dikenali, silakan coba scan lagi.";
                        }
                        
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        utterance.rate = 1.0;
                        utterance.pitch = 1.0;
                        
                        const voices = window.speechSynthesis.getVoices();
                        const idVoice = voices.find(v => v.lang.includes('id') || v.name.includes('Indonesia'));
                        if (idVoice) utterance.voice = idVoice;
                        
                        window.speechSynthesis.speak(utterance);
                    }
                },

                startCountdown() {
                    this.countdown = 5;
                    if (this.countdownInterval) clearInterval(this.countdownInterval);
                    
                    this.countdownInterval = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(this.countdownInterval);
                            this.resetToStep1();
                        }
                    }, 1000);
                },

                // Reset Kiosk — kembali ke layar scan dan langsung nyalakan kamera lagi
                resetToStep1() {
                    if (this.countdownInterval) clearInterval(this.countdownInterval);
                    this.stopCamera();
                    this.step = 1;
                    this.analyzing = false;
                    this.isScanning = false;
                    this.identifiedStudent = null;
                    this.presenceResult = {
                        success: false,
                        message: '',
                        name: '',
                        status: '',
                        confidence: 0.0
                    };
                    this.leaveNotes = '';
                    this.leaveFile = null;
                    this.leaveErrorMsg = '';
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                formatConfidence(val) {
                    return (val * 100).toFixed(1) + '%';
                }
            }
        }
    </script>
</body>
</html>
</body>
</html>
