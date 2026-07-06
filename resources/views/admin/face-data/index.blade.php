@extends('layouts.app')

@section('title', 'Data Wajah Master')
@section('page_title', 'Wajah Master')

@section('content')
<div class="space-y-8">
    <!-- Header Page Description -->
    <div class="card bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="p-3 bg-primary/10 rounded-2xl text-primary mt-0.5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-neutral-800 tracking-tight">
                        Galeri Biometrik Wajah Master
                    </h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">
                        Manajemen dataset wajah latih (master face dataset) yang disinkronkan dengan Python AI Engine untuk rekognisi presensi.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary/5 text-primary border border-primary/20 rounded-2xl text-xs font-bold shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Total: {{ $faceRecords->count() }} Wajah Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Master Biometric Gallery Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($faceRecords as $face)
            <div class="card bg-white border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 group flex flex-col justify-between rounded-3xl">
                
                <!-- Image Wrapper & Preview -->
                <div class="relative aspect-square w-full bg-slate-900 overflow-hidden flex items-center justify-center border-b border-slate-100">
                    <img src="{{ asset('storage/' . $face->image_path) }}" alt="Wajah Master {{ $face->student->user->name }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">
                    
                    <!-- Top Float Badges -->
                    <div class="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none z-10">
                        @if($face->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100/60 shadow-sm">AKTIF</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200/60 shadow-sm">NON-AKTIF</span>
                        @endif

                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/95 backdrop-blur border border-slate-200 text-slate-500 shadow-sm">ID: #{{ $face->id }}</span>
                    </div>

                    <!-- Bottom Overlay (AI Embedding status) -->
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent p-4 flex flex-col justify-end pt-12">
                        <div class="flex items-center gap-1.5 text-emerald-400 font-bold text-[9px] tracking-widest uppercase">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-450 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            AI Embedding Synced
                        </div>
                    </div>
                </div>

                <!-- Info Details -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-neutral-800 text-base leading-tight group-hover:text-primary transition-colors truncate">
                            {{ $face->student->user->name }}
                        </h4>
                        <p class="text-[10px] font-bold font-mono tracking-wider text-slate-400 mt-1 uppercase">NISN: {{ $face->student->nisn }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs font-semibold">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Jurusan</span>
                                <span class="text-neutral-700 font-bold mt-0.5 block truncate">{{ $face->student->department }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Kelas</span>
                                <span class="text-neutral-700 font-bold mt-0.5 block">{{ $face->student->class_name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Panel -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="text-[10px] text-slate-400 font-semibold leading-tight">
                            <span>Daftar:</span>
                            <span class="block text-neutral-700 font-bold mt-0.5">{{ $face->created_at->translatedFormat('d M Y') }}</span>
                        </div>

                        <!-- Delete Button with Alpine confirmation -->
                        <div x-data="{ confirming: false }">
                            <button @click="confirming = true" x-show="!confirming" 
                                class="btn btn-sm bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200/60 rounded-xl font-bold text-[11px] h-8 px-3.5 normal-case shadow-sm transition-all">
                                Hapus
                            </button>
                            
                            <div x-show="confirming" @click.away="confirming = false" class="flex items-center gap-1.5" x-cloak>
                                <form action="{{ route('admin.face-data.destroy', $face->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs bg-rose-600 hover:bg-rose-700 text-white border-transparent rounded-lg font-bold text-[10px] px-2.5 h-6">
                                        Ya
                                    </button>
                                </form>
                                <button @click="confirming = false" class="btn btn-xs bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 rounded-lg font-bold text-[10px] px-2.5 h-6">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <!-- Empty state -->
            <div class="col-span-full card bg-white border border-slate-200/60 p-12 text-center shadow-sm rounded-3xl">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mx-auto mb-4 border border-primary/20 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-pulse text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-lg font-bold text-neutral-800">Belum Ada Wajah Master Terdaftar</h4>
                <p class="text-xs font-semibold text-slate-500 max-w-md mx-auto mt-2">
                    Foto wajah master biometrik akan otomatis muncul di sini setelah Anda mengupload foto master profil siswa atau meng-enroll lewat webcam di menu CRUD Siswa.
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-primary rounded-xl text-xs font-bold normal-case shadow-md shadow-primary/10 hover:shadow-primary/20 transition-all gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Enroll Wajah Siswa Sekarang
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
