<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            
            // Redirect jika tidak sesuai role
            if ($request->user() && $request->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($request->user() && $request->user()->role === 'student') {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('kios-absensi');
            }
            
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk halaman ini.');
        }

        return $next($request);
    }
}
