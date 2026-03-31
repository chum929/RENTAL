@extends('web.layouts.app')
@section('title', 'Daftar Akun')
@section('content')
<div class="max-w-lg mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <div class="text-6xl mb-3">🚗</div>
        <h1 class="text-2xl font-bold">Buat Akun Baru</h1>
        <p class="text-gray-500 mt-1">Daftar sebagai penyewa atau penyedia rental</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-8">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5">
                @foreach($errors->all() as $error)
                    <p class="text-sm">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'user') }}' }">
            @csrf

            <!-- Pilih Role -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar sebagai</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="user" x-model="role" class="sr-only">
                        <div :class="role === 'user'
                                ? 'border-blue-600 bg-blue-50 text-blue-700'
                                : 'border-gray-200 text-gray-600'"
                             class="border-2 rounded-xl p-3 text-center transition">
                            <div class="text-2xl mb-1">👤</div>
                            <p class="font-semibold text-sm">Penyewa</p>
                            <p class="text-xs mt-0.5">Sewa mobil</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="owner" x-model="role" class="sr-only">
                        <div :class="role === 'owner'
                                ? 'border-blue-600 bg-blue-50 text-blue-700'
                                : 'border-gray-200 text-gray-600'"
                             class="border-2 rounded-xl p-3 text-center transition">
                            <div class="text-2xl mb-1">🏢</div>
                            <p class="font-semibold text-sm">Owner</p>
                            <p class="text-xs mt-0.5">Sewakan mobil</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <!-- Nomor HP -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="08xxxxxxxxxx"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <!-- Extra fields untuk owner -->
            <div x-show="role === 'owner'" class="space-y-4 mb-4 p-4 bg-blue-50 rounded-xl">
                <p class="text-sm font-medium text-blue-800">📋 Informasi Rental</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha Rental</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}"
                           placeholder="Contoh: Budi Car Rental"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <select name="city_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5">
                        <option value="">-- Pilih Kota --</option>
                        @foreach(\App\Models\City::where('is_active', true)->get() as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Rental</label>
                    <textarea name="address" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 resize-none">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold
                           hover:bg-blue-700 transition text-base">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-5 text-center">
            <p class="text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                    Login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection