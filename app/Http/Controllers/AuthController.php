<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $logService;

    /**
     * AuthController constructor.
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Catat log aktivitas sukses login
            $this->logService->logActivity($user->id, 'login', "User login berhasil dengan role: {$user->role}");

            // Redirect dinamis berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang Admin!');
            }

            // Jika bukan admin, log out dan kembalikan pesan error formal
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akses ditolak. Hanya akun Administrator Sekolah yang diizinkan masuk ke portal ini.',
            ]);
        }

        // Catat kegagalan login jika user ada
        $this->logService->logActivity(null, 'login_failed', "Percobaan login gagal untuk email: {$request->email}");

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data administrasi kami.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Catat log aktivitas logout
        $this->logService->logActivity($userId, 'logout', "User {$userName} berhasil logout");

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar sistem.');
    }
}
