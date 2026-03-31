@extends('web.layouts.app')
@section('title', 'Dashboard Saya')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <!-- Sambutan -->
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl p-6 text-white mb-8">
        <h1 class="text-2xl font-bold">
            Halo, {{ auth()->user()->name }}! 👋
        </h1>
        <p class="text-blue-100 mt-1">Selamat datang di dashboard penyewa.</p>
        <a href="{{ route('cars.index') }}"
           class="inline-block mt-4 bg-white text-blue-700 font-semibold
                  px-5 py-2 rounded-xl hover:bg-blue-50 transition text-sm">
            🔍 Cari Mobil Sekarang
        </a>
    </div>

    <!-- Notif belum dibaca -->
    @if($unreadNotif > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex
                items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🔔</span>
            <span class="text-yellow-800 font-medium">
                Kamu punya {{ $unreadNotif }} notifikasi belum dibaca
            </span>
        </div>
        <a href="{{ route('user.notifications') }}"
           class="text-sm text-yellow-700 font-semibold hover:underline">
            Lihat →
        </a>
    </div>
    @endif

    <!-- Booking aktif -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800">Booking Aktif</h2>
            <a href="{{ route('user.bookings') }}"
               class="text-sm text-blue-600 hover:underline">
                Lihat semua →
            </a>
        </div>

        @forelse($activeBookings as $booking)
        <div class="bg-white rounded-xl border shadow-sm p-5 mb-3">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-800">
                        {{ $booking->car->name ?? '-' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        📅 {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                        →
                        {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                        ({{ $booking->total_days }} hari)
                    </p>
                    <p class="text-sm text-gray-500">
                        🏢 {{ $booking->car->rentalProvider->business_name ?? '-' }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ match($booking->status) {
                            'pending'  => 'bg-yellow-100 text-yellow-700',
                            'approved' => 'bg-green-100 text-green-700',
                            'ongoing'  => 'bg-blue-100 text-blue-700',
                            default    => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ match($booking->status) {
                            'pending'  => '⏳ Menunggu',
                            'approved' => '✅ Disetujui',
                            'ongoing'  => '🚗 Berlangsung',
                            default    => ucfirst($booking->status),
                        } }}
                    </span>
                    <p class="text-blue-700 font-bold mt-2 text-sm">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="mt-3 flex justify-end">
                <a href="{{ route('user.booking.detail', $booking->id) }}"
                   class="text-sm text-blue-600 hover:underline font-medium">
                    Lihat Detail →
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
            <div class="text-5xl mb-3">🚗</div>
            <p class="text-gray-500">Belum ada booking aktif.</p>
            <a href="{{ route('cars.index') }}"
               class="inline-block mt-3 bg-blue-600 text-white px-5 py-2
                      rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                Cari Mobil Sekarang
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection