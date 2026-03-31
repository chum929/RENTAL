<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RentalProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller {

    public function showLogin() {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user()->role);
        }
        return view('web.auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $user = auth()->user();
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun kamu telah dinonaktifkan.']);
        }

        return $this->redirectByRole($user->role);
    }

    public function showRegister() {
        return view('web.auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:user,owner',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Jika owner, buat profil rental kosong dulu (status pending)
        if ($user->role === 'owner') {
            $request->validate([
                'business_name' => 'required|string',
                'city_id'       => 'required|exists:cities,id',
                'address'       => 'required|string',
            ]);
            RentalProvider::create([
                'user_id'       => $user->id,
                'city_id'       => $request->city_id,
                'business_name' => $request->business_name,
                'address'       => $request->address,
                'phone'         => $request->phone,
                'status'        => 'pending', // menunggu approval admin
            ]);
        }

        Auth::login($user);
        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole(string $role) {
        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}