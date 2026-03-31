@extends('web.layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">👤 Profil Saya</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5">
        @foreach($errors->all() as $error)
            <p class="text-sm">• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <!-- Header foto profil -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-8 text-center">
            @if($user->photo)
                <img src="{{ asset('storage/'.$user->photo) }}"
                     class="w-24 h-24 rounded-full object-cover border-4
                            border-white mx-auto mb-3">
            @else
                <div class="w-24 h-24 bg-blue-300 rounded-full flex items-center
                            justify-center text-4xl font-bold text-white mx-auto mb-3">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <h2 class="text-white font-bold text-xl">{{ $user->name }}</h2>
            <span class="inline-block mt-1 bg-blue-400 text-white text-xs
                         px-3 py-1 rounded-full">
                {{ ucfirst($user->role) }}
            </span>
        </div>

        <!-- Form Edit -->
        <div class="p-6">
            <form method="POST" action="{{ route('user.profile.update') }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full border border-gray-100 rounded-xl px-4 py-2.5
                                      bg-gray-50 text-gray-400 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Email tidak bisa diubah</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor HP
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Foto Profil
                        </label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2
                                      text-sm text-gray-500">
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 w-full bg-blue-600 text-white py-3 rounded-xl
                               font-semibold hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Logout -->
    <div class="mt-4">
        <form method="POST" action="{{ route('logout') }}"
              onsubmit="return confirm('Yakin ingin logout?')">
            @csrf
            <button class="w-full border-2 border-red-200 text-red-600 py-2.5
                           rounded-xl font-semibold hover:bg-red-50 transition text-sm">
                🚪 Logout
            </button>
        </form>
    </div>
</div>
@endsection