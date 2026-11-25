<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password) {
            return redirect()->route('profile.edit')
                ->with('error', 'Anda wajib mengubah kata sandi terlebih dahulu sebelum menggunakan aplikasi.');
        }

        return $next($request);
    }
}
