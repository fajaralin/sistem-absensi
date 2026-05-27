@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md">
    
    <!-- Academic Logo & Header -->
    <div class="text-center mb-8">
        <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-primary via-primary/80 to-blue-600 items-center justify-center text-white shadow-xl shadow-primary/10 mb-4 ring-4 ring-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h1 class="text-xl font-bold tracking-tight text-neutral-800 leading-tight">SMA NEGERI 1 UTAMA</h1>
        <p class="text-[9px] font-bold text-primary uppercase tracking-widest mt-1.5">Sistem Absensi Biometrik Lobi Sekolah</p>
    </div>

    <!-- Formal Login Card -->
    <div class="card bg-white/80 backdrop-blur-lg border border-slate-200/60 shadow-xl relative overflow-hidden rounded-[2rem]">
        
        <!-- Top Primary Color Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-blue-500 to-primary"></div>

        <div class="card-body p-6 md:p-8 space-y-6">
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Form Title -->
                <div class="space-y-1">
                    <h2 class="text-base font-bold text-neutral-800 tracking-wide">Portal Administrasi</h2>
                    <p class="text-xs font-medium text-base-content/50 leading-normal">Masuk menggunakan email dan kata sandi Anda yang terdaftar.</p>
                </div>

                <!-- Error List -->
                @if ($errors->any())
                    <div class="alert alert-error shadow-sm rounded-xl py-3 text-xs font-semibold flex flex-col items-start gap-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-error-content"></span>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Email Input -->
                <div class="form-control w-full space-y-2">
                    <label for="email" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Alamat Email Resmi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </span>
                        <input id="email" name="email" type="email" required placeholder="admin@sman1utama.sch.id" 
                            class="input input-bordered w-full rounded-xl pl-11 text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-control w-full space-y-2">
                    <label for="password" class="label-text text-[10px] font-bold text-base-content/65 uppercase tracking-wider pl-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input id="password" name="password" type="password" required placeholder="••••••" 
                            class="input input-bordered w-full rounded-xl pl-11 text-xs font-semibold focus:border-primary focus:ring-primary/20 bg-white border-slate-200">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2.5">
                        <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-xs rounded-md">
                        <span class="label-text text-xs font-bold text-base-content/60">Ingat Akun Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary rounded-xl w-full text-xs font-bold uppercase tracking-wider shadow-md shadow-primary/20 gap-2">
                    Masuk Sistem
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-[9px] font-bold uppercase tracking-wider">
                    <span class="bg-white px-3 text-base-content/40 rounded-full">Simulasi Kredensial Uji Coba</span>
                </div>
            </div>

            <!-- Quick Login Button for Admin Demo -->
            <div class="space-y-3">
                <button type="button" onclick="document.getElementById('email').value='admin@presensi.com'; document.getElementById('password').value='password'" 
                    class="btn btn-outline btn-primary hover:bg-primary/10 hover:text-primary normal-case rounded-xl w-full h-auto p-4 flex items-center justify-between group shadow-sm bg-primary/5 border-primary/20 text-left transition-all duration-200 hover:scale-[1.01]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-primary block">Kredensial Administrator</span>
                        <span class="text-[10px] font-semibold text-base-content/50 group-hover:text-primary/80 block">admin@presensi.com / password</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:scale-105 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m-5 4a5 5 0 01-10 0 5 5 0 0110 0zm0 0l7 7m0 0l-3-3m3 3l3-3" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
