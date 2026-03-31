@extends('web.layouts.app')
@section('title', 'Booking Saya')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Booking Saya</h1>

    @forelse($bookings as $booking)
    <div class="bg-white rounded-2xl border shadow-sm mb-4 overflow-hidden">
        <!-- Header status -->
        <div class="px-5 py-3 border-b flex justify-between items-center
            {{ match($booking->status) {
                'pending'   => 'bg-yellow-50',
                'approved'  => 'bg-green-50',
                'rejected'  => 'bg-red-50',
                'completed' => 'bg-blue-50',
                'cancelled' => 'bg-gray-50',
                default     => 'bg-gray-50',
            } }}">
            <span class="text-sm font-medium text-gray-600">
                Booking #{{ $booking->id }}
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-bold
                {{ match($booking->status) {
                    'pending'   => 'bg-yellow-100 text-yellow-700',
                    'approved'  => 'bg-green-100 text-green-700',
                    'rejected'  => 'bg-red-100 text-red-700',
                    'completed' => 'bg-blue-100 text-blue-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                    default     => 'bg-gray-100 text-gray-600',
                } }}">
                {{ match($booking->status) {
                    'pending'   => '⏳ Menunggu',
                    'approved'  => '✅ Disetujui',
                    'rejected'  => '❌ Ditolak',
                    'completed' => '🎉 Selesai',
                    'cancelled' => '🚫 Dibatalkan',
                    default     => ucfirst($booking->status),
                } }}
            </span>
        </div>

        <!-- Body -->
        <div class="p-5 flex gap-4">
            <!-- Foto mobil -->
            <div class="flex-shrink-0">
                @if($booking->car && $booking->car->photo)
                    <img src="{{ asset('storage/'.$booking->car->photo) }}"
                         class="w-24 h-20 object-cover rounded-xl">
                @else
                    <div class="w-24 h-20 bg-gray-100 rounded-xl flex
                                items-center justify-center text-3xl">🚗</div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 text-lg">
                    {{ $booking->car->name ?? '-' }}
                </h3>
                <p class="text-sm text-gray-500">
                    🏢 {{ $booking->car->rentalProvider->business_name ?? '-' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    📅 {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                    →
                    {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                    <span class="text-gray-400">({{ $booking->total_days }} hari)</span>
                </p>
            </div>

            <!-- Harga & aksi -->
            <div class="text-right flex-shrink-0">
                <p class="font-bold text-blue-700 text-lg">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
                <a href="{{ route('user.booking.detail', $booking->id) }}"
                   class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                    Detail →
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border shadow-sm p-16 text-center">
        <div class="text-6xl mb-4">📋</div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Booking</h3>
        <p class="text-gray-400 mb-4">Kamu belum pernah melakukan booking.</p>
        <a href="{{ route('cars.index') }}"
           class="inline-block bg-blue-600 text-white px-6 py-2 rounded-xl
                  font-medium hover:bg-blue-700 transition">
            Cari Mobil Sekarang
        </a>
    </div>
    @endforelse

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection