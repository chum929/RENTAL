@extends('web.layouts.owner')
@section('title', 'Dashboard Owner')
@section('content')

<!-- Status approval -->
@if($provider->status === 'pending')
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-xl p-4 mb-6 flex items-center gap-3">
        ⏳ <span>Akun rental kamu sedang menunggu persetujuan admin. Kamu belum bisa menerima booking.</span>
    </div>
@elseif($provider->status === 'rejected')
    <div class="bg-red-50 border border-red-300 text-red-700 rounded-xl p-4 mb-6">
        ❌ Akun rental kamu ditolak. Hubungi admin untuk informasi lebih lanjut.
    </div>
@endif

<!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Total Armada</p>
        <p class="text-3xl font-bold text-blue-700 mt-1">{{ $totalCars }}</p>
        <p class="text-xs text-gray-400 mt-1">🚗 Mobil terdaftar</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Booking Pending</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingBookings }}</p>
        <p class="text-xs text-gray-400 mt-1">⏳ Menunggu konfirmasi</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Status Akun</p>
        <p class="text-xl font-bold mt-1
           {{ $provider->status === 'approved' ? 'text-green-600' : 'text-yellow-600' }}">
           {{ ucfirst($provider->status) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">🏢 {{ $provider->business_name }}</p>
    </div>
</div>

<!-- Booking Terbaru -->
<div class="bg-white rounded-2xl border shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-800">Booking Terbaru</h3>
        <a href="{{ route('owner.bookings') }}" class="text-sm text-blue-600 hover:underline">
            Lihat semua →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-gray-500 text-left">
                    <th class="pb-3 font-medium">Penyewa</th>
                    <th class="pb-3 font-medium">Mobil</th>
                    <th class="pb-3 font-medium">Tanggal</th>
                    <th class="pb-3 font-medium">Total</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($recentBookings as $booking)
                <tr>
                    <td class="py-3 font-medium">{{ $booking->user->name }}</td>
                    <td class="py-3 text-gray-600">{{ $booking->car->name }}</td>
                    <td class="py-3 text-gray-500">
                        {{ \Carbon\Carbon::parse($booking->start_date)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                    </td>
                    <td class="py-3 font-semibold text-blue-700">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ match($booking->status) {
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'approved'  => 'bg-green-100 text-green-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                default     => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="py-3">
                        @if($booking->status === 'pending')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('owner.booking.approve', $booking->id) }}">
                                @csrf
                                <button class="text-xs bg-green-100 text-green-700 px-2 py-1
                                               rounded-lg hover:bg-green-200 transition">
                                    ✓ Terima
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.booking.reject', $booking->id) }}">
                                @csrf
                                <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                               rounded-lg hover:bg-red-200 transition">
                                    ✗ Tolak
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection