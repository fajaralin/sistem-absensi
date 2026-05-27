@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page_title', 'Registrasi Siswa Baru')

@section('content')
<div class="card bg-white border border-slate-200/60 shadow-sm rounded-[2rem] overflow-hidden" x-data="studentCreator()">
    <div class="card-body p-6 md:p-8 space-y-6">
        
        <div class="pb-5 border-b border-slate-100 flex items-start gap-3">
            <div class="p-2.5 bg-primary/10 rounded-xl text-primary mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-neutral-800 text-lg">Form Tambah Siswa Baru</h3>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Registrasikan data diri siswa beserta data biometrik wajah master secara instan</p>
            </div>
        </div>

        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="handleSubmit($event)">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Side: Biodata Form -->
                <div class="lg:col-span-7 space-y-6">
                    <h4 class="text-[11px] font-bold text-primary bg-primary/5 border border-primary/10 rounded-lg px-3 py-1.5 w-fit uppercase tracking-widest pl-3 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        A. Biodata Siswa
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Nama Lengkap -->
                        <div class="form-control w-full space-y-2">
                            <label for="name" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" required placeholder="Budi Handoko" value="{{ old('name') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('name') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NISN -->
                        <div class="form-control w-full space-y-2">
                            <label for="nisn" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">NISN</label>
                            <input type="text" id="nisn" name="nisn" required placeholder="0089123456" value="{{ old('nisn') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('nisn') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email Siswa -->
                        <div class="form-control w-full space-y-2">
                            <label for="email" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Email Siswa</label>
                            <input type="email" id="email" name="email" required placeholder="budi@student.com" value="{{ old('email') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('email') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nomor HP -->
                        <div class="form-control w-full space-y-2">
                            <label for="phone" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Nomor Telepon/HP</label>
                            <input type="text" id="phone" name="phone" placeholder="0812345678" value="{{ old('phone') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('phone') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jurusan/Peminatan -->
                        <div class="form-control w-full space-y-2">
                            <label for="department" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Jurusan / Peminatan</label>
                            <input type="text" id="department" name="department" required placeholder="MIPA / IPS / Bahasa" value="{{ old('department') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('department') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kelas -->
                        <div class="form-control w-full space-y-2">
                            <label for="class_name" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Kelas</label>
                            <input type="text" id="class_name" name="class_name" required placeholder="IF-6A" value="{{ old('class_name') }}"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('class_name') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kata Sandi Akun -->
                        <div class="form-control w-full space-y-2">
                            <label for="password" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Kata Sandi Akun</label>
                            <input type="password" id="password" name="password" required placeholder="••••••" value="password"
                                class="input input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            <span class="text-[9px] font-bold text-slate-400 pl-1 block">Default: password (dapat dirubah sendiri nanti)</span>
                            @error('password') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Foto Profil -->
                        <div class="form-control w-full space-y-2">
                            <label for="photo" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Foto Profil (File)</label>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="file-input file-input-bordered w-full h-11 rounded-xl text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                            @error('photo') <span class="text-xs text-error font-semibold pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Side: Webcam Face Enrollment -->
                <div class="lg:col-span-5 space-y-6">
                    <h4 class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-lg px-3 py-1.5 w-fit uppercase tracking-widest pl-3 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        B. Biometrik Wajah Master
                    </h4>
                    
                    <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Registrasi Kamera</span>
                            <button type="button" @click="toggleCamera()" 
                                class="btn btn-xs bg-white hover:bg-slate-50 text-slate-700 border-slate-200 hover:border-slate-300 normal-case rounded-lg text-[10px] font-bold px-3 transition-all"
                                x-text="isCamActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></button>
                        </div>

                        <!-- Camera Feed aspect -->
                        <div class="relative aspect-video w-full rounded-2xl bg-slate-900 overflow-hidden flex items-center justify-center border border-slate-200/80 shadow-sm">
                            <video id="createWebcam" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]" x-show="isCamActive && !hasCaptured"></video>
                            <img id="capturePreview" class="w-full h-full object-cover" x-show="hasCaptured">

                            <!-- Scanning crosshair guide -->
                            <div class="absolute inset-0 border-2 border-dashed border-primary/50 rounded-2xl m-6 pointer-events-none" x-show="isCamActive && !hasCaptured">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-24 h-24 rounded-full border border-primary/40 bg-primary/5 backdrop-blur-[1px]"></div>
                                </div>
                            </div>

                            <!-- Disabled message -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-slate-100/90 backdrop-blur-sm text-slate-400 gap-3 rounded-2xl border border-dashed border-slate-200/60" x-show="!isCamActive && !hasCaptured">
                                <div class="p-3 bg-white text-slate-300 rounded-full border border-slate-200/60 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 max-w-[200px] leading-relaxed uppercase tracking-wider">Kamera mati. Klik tombol di atas untuk mendaftarkan biometric.</span>
                            </div>
                        </div>

                        <!-- Hidden input to hold base64 image data -->
                        <input type="hidden" id="face_image" name="face_image" x-model="faceImageBase64">

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button type="button" @click="capturePhoto()" x-show="isCamActive && !hasCaptured"
                                class="btn btn-sm btn-primary btn-block rounded-xl font-bold uppercase text-xs tracking-wider h-10 shadow-md shadow-primary/20">
                                Ambil Gambar Wajah
                            </button>
                            <button type="button" @click="retakePhoto()" x-show="hasCaptured"
                                class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200 hover:border-slate-350 btn-block rounded-xl font-bold uppercase text-xs tracking-wider h-10">
                                Ambil Ulang Foto
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Hidden canvas for capture logic -->
            <canvas id="createCanvas" class="hidden" width="640" height="480"></canvas>

            <!-- Form Submission Footer -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.students.index') }}" class="btn btn-sm bg-slate-50 hover:bg-slate-100 text-slate-600 border-slate-200 rounded-xl px-5 h-10 normal-case text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="btn btn-sm btn-primary rounded-xl px-6 h-10 normal-case text-xs font-bold shadow-md shadow-primary/20 transition-all">
                    Simpan & Registrasi Siswa
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function studentCreator() {
        return {
            isCamActive: false,
            hasCaptured: false,
            faceImageBase64: '',
            stream: null,

            async startCamera() {
                const video = document.getElementById('createWebcam');
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { width: 640, height: 480 }
                    });
                    video.srcObject = this.stream;
                    this.isCamActive = true;
                    this.hasCaptured = false;
                } catch (error) {
                    console.error("Camera start failed:", error);
                    this.isCamActive = false;
                }
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
                this.isCamActive = false;
            },

            toggleCamera() {
                if (this.isCamActive) {
                    this.stopCamera();
                } else {
                    this.startCamera();
                }
            },

            capturePhoto() {
                const video = document.getElementById('createWebcam');
                const canvas = document.getElementById('createCanvas');
                const context = canvas.getContext('2d');
                const preview = document.getElementById('capturePreview');

                // UNFLIPPED - keeping orientation aligned with training dataset configuration
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const base64Data = canvas.toDataURL('image/jpeg');

                this.faceImageBase64 = base64Data;
                preview.src = base64Data;
                this.hasCaptured = true;
                
                this.stopCamera();
            },

            retakePhoto() {
                this.hasCaptured = false;
                this.faceImageBase64 = '';
                this.startCamera();
            },

            handleSubmit(e) {
                // Biarkan form melakukan submit standard
                this.stopCamera();
            }
        }
    }
</script>
@endsection

