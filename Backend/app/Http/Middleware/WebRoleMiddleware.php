<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebRoleMiddleware {
    public function handle(Request $request, Closure $next, string $role): mixed {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (auth()->user()->role !== $role) {
            // Arahkan ke dashboard sesuai role masing-masing
            return match(auth()->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'owner' => redirect()->route('owner.dashboard'),
                'user'  => redirect()->route('user.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}