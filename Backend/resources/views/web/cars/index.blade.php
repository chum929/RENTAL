@extends('web.layouts.app')
@section('title', 'Daftar Mobil')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Cari Mobil Sewa</h1>

    <!-- FILTER PANEL -->
    <form method="GET" action="{{ route('cars.index') }}"
          class="bg-white rounded-2xl shadow-sm border p-5 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍 Nama mobil..."
                   class="col-span-2 md:col-span-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">

            <select name="city_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="">📍 Semua Kota</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="">🚗 Semua Tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="number" name="min_price" value="{{ request('min_price') }}"
                       placeholder="Harga min"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <input type="number" name="max_price" value="{{ request('max_price') }}"
                       placeholder="Harga maks"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <button class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm
                           font-medium hover:bg-blue-700 transition">
                Filter
            </button>
        </div>
        @if(request()->anyFilled(['search','city_id','type','min_price','max_price']))
            <div class="mt-3">
                <a href="{{ route('cars.index') }}"
                   class="text-sm text-gray-500 hover:text-red-500">✕ Reset filter</a>
            </div>
        @endif
    </form>

    <!-- HASIL -->
    <div class="flex justify-between items-center mb-4">
        <p class="text-gray-500 text-sm">{{ $cars->total() }} mobil ditemukan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($cars as $car)
            <div class="bg-white rounded-2xl border shadow-sm hover:shadow-md
                        transition-shadow overflow-hidden group">
                <div class="h-40 bg-gray-100 overflow-hidden">
                    @if($car->photo)
                        <img src="{{ asset('storage/'.$car->photo) }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-5xl">🚗</div>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="font-bold text-gray-800">{{ $car->name }}</h3>
                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                            {{ $car->type }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-1">
                        👥 {{ $car->seats }} kursi &nbsp;·&nbsp;
                        📍 {{ $car->rentalProvider->city->name ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-500 mb-3">
                        🏢 {{ $car->rentalProvider->business_name ?? '-' }}
                    </p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-blue-700 font-bold">
                                Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-400 text-xs">/hari</span>
                        </div>
                        <a href="{{ route('cars.show', $car->id) }}"
                           class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs
                                  hover:bg-blue-700 transition font-medium">
                            Pesan
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-gray-400">
                <div class="text-6xl mb-4">🔍</div>
                <p>Tidak ada mobil yang sesuai filter.</p>
                <a href="{{ route('cars.index') }}" class="text-blue-600 mt-2 inline-block">Reset filter</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $cars->links() }}
    </div>
</div>
@endsection