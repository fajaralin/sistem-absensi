@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('page_title', 'Manajemen Data Siswa')

@section('content')
<div class="card bg-white border border-slate-200/60 shadow-sm rounded-3xl" x-data="faceEnrollmentManager()">
    <div class="card-body p-6 md:p-8 space-y-6">
        
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-start gap-3">
                <div class="p-2.5 bg-primary/10 rounded-xl text-primary mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-neutral-800 text-lg">Daftar Siswa Terdaftar</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Kelola data profil akademik dan data biometrik wajah master</p>
                </div>
            </div>
            <a href="{{ route('admin.students.create') }}" class="btn btn-md bg-primary hover:bg-primary/90 text-white border-none normal-case rounded-xl font-bold text-xs shadow-md shadow-primary/10 hover:shadow-primary/20 transition-all duration-150 w-full sm:w-auto flex items-center gap-1.5 justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Siswa Baru
            </a>
        </div>

        <!-- Student Table -->
        <div class="overflow-x-auto w-full">
            <table class="table w-full">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/60 text-slate-500 font-bold text-[11px] uppercase tracking-wider">
                        <th class="px-6 py-4">Nama / NISN</th>
                        <th class="px-6 py-4">Jurusan / Kelas</th>
                        <th class="px-6 py-4">Kontak / Email</th>
                        <th class="px-6 py-4">Wajah Master</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-semibold text-sm text-neutral-700 divide-y divide-slate-100">
                    @forelse($students as $st)
                        <tr class="hover:bg-slate-50/45 transition-colors">
                            <!-- Name & NIM -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shadow-sm overflow-hidden">
                                            @if($st->photo_path)
                                                <img src="{{ asset('storage/' . $st->photo_path) }}" alt="Foto Profil" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-indigo-600 text-sm font-bold">{{ substr($st->user->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="block text-neutral-800 font-bold leading-tight">{{ $st->user->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-semibold tracking-wide block mt-0.5">NISN: {{ $st->nisn }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Department & Class -->
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-slate-700">{{ $st->department }}</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 uppercase tracking-wider mt-1.5">Kelas: {{ $st->class_name }}</span>
                            </td>
                            
                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <span class="block text-xs font-bold text-slate-600">{{ $st->user->email }}</span>
                                <span class="text-[10px] text-slate-400 mt-1 block font-mono font-medium">{{ $st->phone ?? 'Tidak ada kontak' }}</span>
                            </td>
                            
                            <!-- Face Data Status -->
                            <td class="px-6 py-4">
                                @php
                                    $hasActiveFace = $st->faceData->where('status', 'active')->count() > 0;
                                @endphp
                                @if($hasActiveFace)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Ready
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                        Belum Register
                                    </span>
                                @endif
                            </td>

                            <!-- Account Status -->
                            <td class="px-6 py-4">
                                @if($st->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/50">Nonaktif</span>
                                @endif
                            </td>
                            
                            <!-- Actions Dropdown -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Register Face Icon Button -->
                                    <button type="button" @click="openEnrollModal('{{ $st->id }}', '{{ $st->user->name }}', '{{ $st->nisn }}')" 
                                        class="btn btn-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-none rounded-lg font-bold px-3 py-1.5 text-[11px] shadow-sm transition-all duration-150 flex items-center gap-1"
                                        title="Pendaftaran biometric wajah master">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Wajah
                                    </button>
                                    
                                    <a href="{{ route('admin.students.edit', $st->id) }}" class="btn btn-xs bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200/80 rounded-lg font-bold px-3 py-1.5 text-[11px] shadow-sm transition-all duration-150 flex items-center">
                                        Ubah
                                    </a>

                                    <form action="{{ route('admin.students.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini dan seluruh data terkait? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs bg-rose-50 hover:bg-rose-100 text-rose-700 border-none rounded-lg font-bold px-3 py-1.5 text-[11px] shadow-sm transition-all duration-150 flex items-center">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400 font-bold">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-50 text-slate-300 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400 tracking-wide uppercase">Tidak ada siswa terdaftar.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- QUICK ENROLL FACE WEBCAM MODAL (DaisyUI style) -->
        <div class="modal" :class="{ 'modal-open': modalOpen }" x-cloak>
            <div class="modal-box max-w-lg bg-white border border-slate-200/60 relative rounded-[2rem] p-6 shadow-2xl space-y-6">
                <!-- Top Primary Color Bar -->
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-blue-500 to-primary"></div>
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-800 text-base tracking-wide">Registrasi Wajah Master</h3>
                        <p class="text-xs font-medium text-slate-500">Siswa: <span class="font-bold text-slate-700" x-text="enrollName"></span> (NISN: <span class="font-mono text-primary font-bold" x-text="enrollNim"></span>)</p>
                    </div>
                    <button type="button" @click="closeModal()" class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <!-- Webcam Preview Block -->
                <div class="relative aspect-video w-full rounded-2xl bg-slate-900 overflow-hidden flex items-center justify-center ring-1 ring-slate-200 shadow-inner">
                    <video id="enrollWebcam" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]" x-show="isCamActive"></video>
                    
                    <!-- Face framing guide overlay -->
                    <div class="absolute inset-0 border-2 border-dashed border-primary/40 rounded-2xl m-8 pointer-events-none" x-show="isCamActive && !isSaving"></div>
                    
                    <!-- Disabled cam warning -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center bg-slate-50 text-slate-600 gap-3" x-show="!isCamActive">
                        <span class="text-xs font-semibold text-slate-500">Kamera dinonaktifkan</span>
                        <button type="button" @click="startEnrollCamera()" class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-xl font-bold text-xs hover:scale-102 transition-all">
                            Nyalakan Kamera
                        </button>
                    </div>

                    <!-- Saving overlay -->
                    <div class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center text-center p-4 text-slate-800 gap-3 animate-fade-in" x-show="isSaving">
                        <span class="loading loading-ring loading-md text-primary"></span>
                        <span class="text-xs font-bold text-primary uppercase tracking-wider">Menyimpan & Mensinkronkan Wajah...</span>
                    </div>
                </div>

                <!-- Actions block -->
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-semibold text-slate-400 max-w-xs leading-relaxed">Pastikan pencahayaan cukup dan wajah tegak lurus menghadap kamera.</span>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="closeModal()" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 border-none rounded-xl text-xs font-bold transition-all px-4">
                            Batal
                        </button>
                        <button type="button" @click="captureAndEnroll()" :disabled="!isCamActive || isSaving"
                            class="btn btn-sm bg-primary hover:bg-primary/90 text-white border-none rounded-xl text-xs font-bold hover:scale-102 transition-all px-4">
                            Capture & Simpan
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop bg-slate-900/40 backdrop-blur-sm" @click="closeModal()"></div>
        </div>
        
        <!-- Hidden Canvas -->
        <canvas id="enrollCanvas" class="hidden" width="640" height="480"></canvas>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function faceEnrollmentManager() {
        return {
            modalOpen: false,
            enrollStudentId: null,
            enrollName: '',
            enrollNim: '',
            isCamActive: false,
            isSaving: false,
            stream: null,

            openEnrollModal(id, name, nim) {
                this.enrollStudentId = id;
                this.enrollName = name;
                this.enrollNim = nim;
                this.modalOpen = true;
                
                // Nyalakan kamera otomatis
                this.startEnrollCamera();
            },

            async startEnrollCamera() {
                setTimeout(async () => {
                    const video = document.getElementById('enrollWebcam');
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 640, height: 480 }
                        });
                        video.srcObject = this.stream;
                        this.isCamActive = true;
                    } catch (error) {
                        console.error("Camera open failed:", error);
                        this.isCamActive = false;
                        alert('Tidak dapat mendeteksi webcam. Silakan berikan izin akses kamera.');
                    }
                }, 100);
            },

            stopEnrollCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
                this.isCamActive = false;
            },

            closeModal() {
                this.stopEnrollCamera();
                this.modalOpen = false;
            },

            captureAndEnroll() {
                const video = document.getElementById('enrollWebcam');
                const canvas = document.getElementById('enrollCanvas');
                const context = canvas.getContext('2d');

                // UNFLIPPED - keeping orientation aligned with training dataset configuration
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const base64Data = canvas.toDataURL('image/jpeg');

                this.isSaving = true;

                // POST ke Route register-face admin
                fetch(`/admin/students/${this.enrollStudentId}/register-face`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image: base64Data
                    })
                })
                .then(async response => {
                    const res = await response.json();
                    if (response.ok) {
                        alert('Wajah master berhasil terdaftar & tersinkronisasi ke Python Engine!');
                        this.closeModal();
                        window.location.reload();
                    } else {
                        alert('Pendaftaran wajah gagal: ' + res.message);
                    }
                })
                .catch(error => {
                    console.error("Enroll request failed:", error);
                    alert('Terjadi kesalahan koneksi saat mengirim data wajah.');
                })
                .finally(() => {
                    this.isSaving = false;
                });
            }
        }
    }
</script>
@endsection
