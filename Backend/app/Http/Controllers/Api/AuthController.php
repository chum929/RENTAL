<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RentalProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {

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

        // Jika owner, buat profil rental
        if ($user->role === 'owner' && $request->business_name) {
            RentalProvider::create([
                'user_id'       => $user->id,
                'city_id'       => $request->city_id ?? 1,
                'business_name' => $request->business_name,
                'address'       => $request->address ?? '-',
                'phone'         => $user->phone,
                'status'        => 'pending',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil!',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Akun kamu dinonaktifkan.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'token'   => $token,
            'user'    => $user->load('rentalProvider'),
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request) {
        return response()->json($request->user()->load('rentalProvider'));
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);
        $request->user()->update($request->only('name', 'phone'));
        return response()->json([
            'message' => 'Profil diperbarui.',
            'user'    => $request->user(),
        ]);
    }

    public function uploadPhoto(Request $request) {
        $request->validate(['photo' => 'required|image|max:2048']);
        $path = $request->file('photo')->store('profiles', 'public');
        $request->user()->update(['photo' => $path]);
        return response()->json([
            'message' => 'Foto diperbarui.',
            'photo'   => asset('storage/'.$path),
        ]);
    }
}