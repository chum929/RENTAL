@extends('web.layouts.app')
@section('title', 'Login')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-3">🚗</div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali!</h1>
            <p class="text-gray-500 mt-1">Login ke akun RentCar kamu</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="email@example.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required autofocus>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                           placeholder="Masukkan password"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                <div class="flex items-center justify-between mb-5">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded">
                        Ingat saya
                    </label>
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold
                               hover:bg-blue-700 transition text-base">
                    Login
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                        Daftar sekarang
                    </a>
                </p>
            </div>

            <!-- Link ke admin -->
            <div class="mt-4 pt-4 border-t text-center">
                <a href="{{ route('admin.login') }}" class="text-xs text-gray-400 hover:text-gray-600">
                    Login sebagai Admin →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection