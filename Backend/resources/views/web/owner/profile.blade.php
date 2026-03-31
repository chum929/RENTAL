@extends('web.layouts.owner')
@section('title', 'Profil Rental')
@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 p-6 text-white">
            <div class="flex items-center gap-4">
                @if($provider->photo)
                    <img src="{{ asset('storage/'.$provider->photo) }}"
                         class="w-16 h-16 rounded-xl object-cover border-2 border-white">
                @else
                    <div class="w-16 h-16 bg-blue-400 rounded-xl flex items-center
                                justify-center text-3xl">🏢</div>
                @endif
                <div>
                    <h2 class="text-xl font-bold">{{ $provider->business_name }}</h2>
                    <p class="text-blue-100 text-sm">
                        📍 {{ $provider->city->name ?? '-' }} ·
                        <span class="px-2 py-0.5 bg-white bg-opacity-20 rounded-full text-xs">
                            {{ ucfirst($provider->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form method="POST" action="{{ route('owner.profile.update') }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Usaha Rental
                        </label>
                        <input type="text" name="business_name"
                               value="{{ old('business_name', $provider->business_name) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <textarea name="address" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   resize-none focus:ring-2 focus:ring-blue-500">{{ old('address', $provider->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Kontak
                        </label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $provider->phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Foto Usaha
                        </label>
                        @if($provider->photo)
                        <img src="{{ asset('storage/'.$provider->photo) }}"
                             class="h-24 w-auto rounded-xl mb-2 object-cover">
                        @endif
                        <input type="file" name="photo" accept="image/*"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2
                                      text-sm text-gray-500">
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 w-full bg-blue-600 text-white py-2.5 rounded-xl
                               font-semibold hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Info akun login -->
    <div class="mt-5 bg-white rounded-2xl border shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-3">Akun Login</h3>
        <div class="space-y-2 text-sm text-gray-600">
            <p>👤 {{ auth()->user()->name }}</p>
            <p>✉️ {{ auth()->user()->email }}</p>
            <p>📱 {{ auth()->user()->phone }}</p>
        </div>
    </div>
</div>
@endsection