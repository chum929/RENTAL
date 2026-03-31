@extends('web.layouts.app')
@section('title', 'Beranda')
@section('content')

<!-- HERO SECTION -->
<section class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                Sewa Mobil Mudah,<br>
                <span class="text-yellow-300">Terpercaya & Murah</span>
            </h1>
            <p class="text-blue-100 text-lg mb-8">
                Pilih dari ribuan armada di seluruh kota. Booking langsung, tanpa sopir,
                sesuai kebutuhanmu.
            </p>
            <!-- Search box mini di hero -->
            <form action="{{ route('cars.index') }}" method="GET"
                  class="bg-white rounded-2xl p-4 flex flex-col sm:flex-row gap-3 shadow-xl">
                <select name="city_id"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 text-sm">
                    <option value="">📍 Semua Kota</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" placeholder="Nama mobil..."
                       class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 text-sm">
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700
                               transition font-medium text-sm whitespace-nowrap">
                    🔍 Cari Mobil
                </button>
            </form>
        </div>
        <div class="hidden md:flex justify-center">
            <div class="text-9xl">🚗</div>
        </div>
    </div>
</section>

<!-- STATISTIK -->
<section class="bg-white py-8 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-3xl font-bold text-blue-700">{{ $totalCars }}+</p>
                <p class="text-gray-500 text-sm mt-1">Armada Tersedia</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-blue-700">{{ $totalProviders }}+</p>
                <p class="text-gray-500 text-sm mt-1">Penyedia Rental</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-blue-700">{{ $cities->count() }}+</p>
                <p class="text-gray-500 text-sm mt-1">Kota Tersedia</p>
            </div>
        </div>
    </div>
</section>

<!-- MOBIL UNGGULAN -->
<section class="max-w-7xl mx-auto px-4 py-14">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Mobil Pilihan</h2>
            <p class="text-gray-500 mt-1">Armada terbaik dari penyedia terpercaya</p>
        </div>
        <a href="{{ route('cars.index') }}"
           class="text-blue-600 font-medium hover:underline text-sm">
            Lihat semua →
        </a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredCars as $car)
            <div class="bg-white rounded-2xl shadow-sm border hover:shadow-md
                        transition-shadow overflow-hidden group">
                <!-- Foto -->
                <div class="h-44 bg-gray-100 overflow-hidden">
                    @if($car->photo)
                        <img src="{{ asset('storage/'.$car->photo) }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl">🚗</div>
                    @endif
                </div>
                <!-- Info -->
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-lg">{{ $car->name }}</h3>
                        <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">
                            {{ $car->type }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                        <span>👥 {{ $car->seats }} kursi</span>
                        <span>📅 {{ $car->year }}</span>
                        @if($car->rentalProvider)
                            <span>📍 {{ $car->rentalProvider->city->name ?? '' }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-blue-700 font-bold text-lg">
                                Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-400 text-sm">/hari</span>
                        </div>
                        <a href="{{ route('cars.show', $car->id) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm
                                  hover:bg-blue-700 transition font-medium">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- CARA KERJA -->
<section class="bg-blue-50 py-14">
    <div class="max-w-5xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold mb-2">Cara Booking Mudah</h2>
        <p class="text-gray-500 mb-10">Hanya 4 langkah untuk mendapatkan mobil sewaan</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([
                ['🔍', 'Cari Mobil', 'Pilih kota, tanggal, dan filter sesuai kebutuhan'],
                ['📋', 'Pilih & Booking', 'Pilih mobil favorit dan isi form booking'],
                ['✅', 'Konfirmasi Owner', 'Tunggu konfirmasi dari penyedia rental'],
                ['🚗', 'Ambil Mobil', 'Ambil mobil dan nikmati perjalanan'],
            ] as [$icon, $title, $desc])
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="text-4xl mb-3">{{ $icon }}</div>
                <h4 class="font-bold text-gray-800 mb-1">{{ $title }}</h4>
                <p class="text-gray-500 text-sm">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA DAFTAR JADI OWNER -->
<section class="max-w-4xl mx-auto px-4 py-14 text-center">
    <h2 class="text-2xl font-bold mb-3">Punya Mobil Nganggur?</h2>
    <p class="text-gray-500 mb-6">Daftarkan mobilmu dan mulai raup penghasilan dari rental!</p>
    <a href="{{ route('register') }}?role=owner"
       class="inline-block bg-yellow-400 text-yellow-900 px-8 py-3 rounded-xl
              font-bold hover:bg-yellow-500 transition text-lg">
        Daftar sebagai Owner 🏢
    </a>
</section>
@endsection