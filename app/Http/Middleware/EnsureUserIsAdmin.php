<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (!Auth::user()->isAdmin()) {
            if (Auth::user()->isJudge()) {
                return redirect()->route('judge.dashboard')
                    ->with('error', 'Halaman tersebut hanya dapat diakses oleh Administrator/Panitia.');
            }

            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
