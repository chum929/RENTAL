@extends('web.layouts.app')
@section('title', $car->name)
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
        <span class="mx-2">›</span>
        <a href="{{ route('cars.index') }}" class="hover:text-blue-600">Daftar Mobil</a>
        <span class="mx-2">›</span>
        <span class="text-gray-800">{{ $car->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- KIRI: Info Mobil -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Foto -->
            <div class="bg-white rounded-2xl overflow-hidden border shadow-sm">
                @if($car->photo)
                    <img src="{{ asset('storage/'.$car->photo) }}"
                         class="w-full h-72 object-cover">
                @else
                    <div class="w-full h-72 bg-gray-100 flex items-center justify-center text-8xl">🚗</div>
                @endif
            </div>

            <!-- Detail Info -->
            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $car->name }}</h1>
                        <p class="text-gray-500">{{ $car->type }} · {{ $car->year }} · {{ $car->seats }} kursi</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $car->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $car->is_available ? '✅ Tersedia' : '❌ Tidak Tersedia' }}
                    </span>
                </div>

                <!-- Spesifikasi -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    @foreach([
                        ['🚘', 'Plat', $car->plate_number],
                        ['👥', 'Kapasitas', $car->seats.' orang'],
                        ['📅', 'Tahun', $car->year],
                    ] as [$icon, $label, $val])
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <div class="text-2xl mb-1">{{ $icon }}</div>
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $val }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- Deskripsi -->
                @if($car->description)
                    <div class="mb-4">
                        <h3 class="font-semibold mb-2">Deskripsi</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $car->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Info Penyedia -->
            @if($car->rentalProvider)
            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="font-semibold mb-4">Penyedia Rental</h3>
                <div class="flex items-center gap-4">
                    @if($car->rentalProvider->photo)
                        <img src="{{ asset('storage/'.$car->rentalProvider->photo) }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-blue-100">
                    @else
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-2xl">🏢</div>
                    @endif
                    <div>
                        <h4 class="font-bold">{{ $car->rentalProvider->business_name }}</h4>
                        <p class="text-sm text-gray-500">
                            📍 {{ $car->rentalProvider->city->name ?? '' }} · 
                            📱 {{ $car->rentalProvider->phone }}
                        </p>
                        <div class="flex items-center gap-1 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                            @endfor
                            <span class="text-sm text-gray-500 ml-1">
                                {{ number_format($avgRating, 1) }} ({{ $totalReviews }} ulasan)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ulasan -->
            @if($car->rentalProvider && $car->rentalProvider->reviews->isNotEmpty())
            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="font-semibold mb-4">Ulasan Penyewa ({{ $totalReviews }})</h3>
                <div class="space-y-4">
                    @foreach($car->rentalProvider->reviews->take(5) as $review)
                    <div class="border-b pb-4 last:border-0">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-medium text-sm">{{ $review->user->name }}</span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- KANAN: Panel Booking -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border shadow-sm p-6 sticky top-20">
                <div class="text-center mb-6">
                    <span class="text-3xl font-bold text-blue-700">
                        Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-400 text-sm"> / hari</span>
                </div>

                @auth
                    @if(auth()->user()->role === 'user')
                        @if($car->is_available)
                            <a href="{{ route('user.booking.form', $car->id) }}"
                               class="block w-full bg-blue-600 text-white text-center py-3 rounded-xl
                                      font-semibold hover:bg-blue-700 transition text-lg">
                                🚗 Pesan Sekarang
                            </a>
                        @else
                            <button disabled
                                    class="block w-full bg-gray-300 text-gray-500 text-center py-3
                                           rounded-xl font-semibold cursor-not-allowed">
                                Tidak Tersedia
                            </button>
                        @endif
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-sm text-yellow-700">
                            Login sebagai User untuk memesan
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full bg-blue-600 text-white text-center py-3 rounded-xl
                              font-semibold hover:bg-blue-700 transition">
                        Login untuk Memesan
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full mt-3 border-2 border-blue-600 text-blue-600 text-center
                              py-3 rounded-xl font-semibold hover:bg-blue-50 transition">
                        Daftar Gratis
                    </a>
                @endauth

                <!-- Info singkat -->
                <div class="mt-6 space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="text-green-500">✓</span> Tanpa sopir
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-green-500">✓</span> Konfirmasi cepat dari owner
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-green-500">✓</span> Bisa dibatalkan jika pending
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection