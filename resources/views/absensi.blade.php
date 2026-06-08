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
                <div class="inline-flex p-4 bg-primary/10 rounded-2xl text-primary border border-primary/20 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2M3 17v2a2 2 0 002 2h2m10-16h2a2 2 0 012 2v2m-4 14h2a2 2 0 002-2v-2M9 11a3 3 0 106 0 3 3 0 00-6 0zm-3 7c0-1.657 2.686-3 6-3s6 1.343 6 3" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold tracking-tight text-base-content">Pindai Wajah Anda</h2>
                <p class="text-xs text-base-content/55 max-w-md mx-auto leading-relaxed font-medium">
                    Tidak perlu mengisi nama atau NISN — cukup posisikan wajah Anda di depan kamera, sistem akan otomatis mengenali identitas Anda dan mencatat kehadiran.
                </p>
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
                        <h4 class="text-sm font-bold text-neutral-800 tracking-tight uppercase">Mengidentifikasi Wajah Anda...</h4>
                        <p class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider">LBPH Biometric AI Face Matcher (1-to-Many)</p>
                    </div>
                    <progress class="progress progress-primary w-56" value="100" max="100"></progress>
                </div>
            </div>

            <!-- Capture Action Panel -->
            <div class="w-full flex flex-col sm:flex-row gap-4 items-center justify-between">
                <p class="text-center sm:text-left text-xs font-medium text-base-content/50 max-w-xs leading-relaxed">
                    Posisikan wajah Anda tepat di dalam lingkaran. Pastikan pencahayaan terang dan hadapkan wajah lurus ke arah kamera.
                </p>
                <button @click="captureAndScan" :disabled="analyzing"
                    class="btn btn-primary px-8 rounded-2xl text-xs font-bold text-white tracking-wide hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 shadow-md shadow-primary/20 gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0zm6-9h.01M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                    Scan Sekarang
                </button>
            </div>
            </div>
        </div>

        <!-- STEP 3: Feedback Overlay Pop-Up -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="card w-full max-w-md bg-white/95 backdrop-blur-md border border-base-200 shadow-2xl relative biometric-glow overflow-hidden rounded-[2rem]">
            <div class="card-body p-8 text-center space-y-6">
            
            <!-- Success Icon & Header (Hadir tepat waktu) -->
            <template x-if="presenceResult.success && presenceResult.status !== 'telat'">
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
                Reset Antrean Sekarang
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

                initKiosk() {
                    // Start digital clock
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    // Kamera langsung aktif begitu halaman terbuka — siswa tinggal scan tanpa input apapun
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                updateTime() {
                    const now = new Date();
                    
                    // Time format: HH:mm:ss
                    this.currentTime = now.toTimeString().split(' ')[0];
                    
                    // Date format: Senin, 20 Mei 2026
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    this.currentDate = now.toLocaleDateString('id-ID', options);
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
                    } catch (err) {
                        console.error("Gagal membuka kamera:", err);
                        alert("Kamera webcam tidak terdeteksi atau tidak diizinkan oleh browser.");
                        this.resetToStep1();
                    }
                },

                stopCamera() {
                    if (this.webcamStream) {
                        this.webcamStream.getTracks().forEach(track => track.stop());
                        this.webcamStream = null;
                    }
                    const video = document.getElementById('webcam');
                    if (video) video.srcObject = null;
                },

                // Capture snapshot and send to backend
                async captureAndScan() {
                    const video = document.getElementById('webcam');
                    const canvas = document.getElementById('canvas');
                    if (!video || !canvas) return;

                    this.analyzing = true;

                    // Draw image on canvas (UNFLIPPED - keeping the horizontal direction aligned with face model)
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Get base64 string
                    const imageBase64 = canvas.toDataURL('image/jpeg', 0.85);

                    try {
                        const response = await fetch('/absensi/scan-auto', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                image: imageBase64
                            })
                        });

                        const result = await response.json();
                        
                        this.presenceResult = {
                            success: result.success,
                            message: result.message,
                            name: result.name || '',
                            status: result.status || '',
                            confidence: result.confidence || 0.0
                        };

                        this.stopCamera();
                        this.step = 3;
                        this.analyzing = false;

                        // Voice response (Text-To-Speech)
                        this.speakFeedback(this.presenceResult);

                        // Start countdown timer to reset page
                        this.startCountdown();

                    } catch (error) {
                        console.error('Scan presence failed:', error);
                        
                        this.presenceResult = {
                            success: false,
                            message: 'Presensi gagal karena gangguan koneksi API Server.',
                            name: '',
                            confidence: 0.0
                        };
                        
                        this.stopCamera();
                        this.step = 3;
                        this.analyzing = false;
                        
                        this.speakFeedback(this.presenceResult);
                        this.startCountdown();
                    }
                },

                // Speaks aloud presence status
                speakFeedback(result) {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel(); // Cancel any ongoing voices
                        
                        let text = '';
                        if (result.success && result.status === 'telat') {
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
                        
                        // Select an Indonesian voice if available
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
