<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsJudge
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (!Auth::user()->isJudge()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Halaman tersebut dikhususkan untuk akun Dewan Juri.');
            }

            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
