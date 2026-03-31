<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\RentalProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {

    public function showLogin() {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (auth()->user()->isAdmin()) {
                return redirect('/admin/dashboard');
            }
            Auth::logout();
        }
        return back()->withErrors(['email' => 'Email/password salah atau bukan admin.']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard() {
        return view('admin.dashboard', [
            'stats' => [
                'users'     => User::where('role', 'user')->count(),
                'owners'    => User::where('role', 'owner')->count(),
                'providers' => RentalProvider::where('status', 'approved')->count(),
                'cars'      => Car::count(),
                'bookings'  => Booking::count(),
            ],
            'pendingProviders' => RentalProvider::where('status', 'pending')
                ->with('city','user')->latest()->take(5)->get(),
            'recentBookings'   => Booking::with(['user','car'])
                ->latest()->take(5)->get(),
        ]);
    }
}