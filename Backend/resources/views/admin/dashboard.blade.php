@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['Total User',      $stats['users'],     'bg-blue-500'],
        ['Total Owner',     $stats['owners'],    'bg-indigo-500'],
        ['Rental Aktif',    $stats['providers'], 'bg-green-500'],
        ['Total Mobil',     $stats['cars'],      'bg-yellow-500'],
        ['Total Booking',   $stats['bookings'],  'bg-purple-500'],
    ] as [$label, $val, $color])
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 {{ $color }}">
        <p class="text-xs text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $val }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Penyedia Pending -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">🔔 Penyedia Menunggu Approval</h3>
            <a href="/admin/providers" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
        </div>
        @forelse($pendingProviders as $p)
            <div class="flex justify-between items-center py-3 border-b last:border-0">
                <div>
                    <p class="font-medium text-sm">{{ $p->business_name }}</p>
                    <p class="text-xs text-gray-500">{{ $p->city->name }} · {{ $p->user->name }}</p>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="/admin/providers/{{ $p->id }}/approve">
                        @csrf
                        <button class="text-xs bg-green-100 text-green-700 px-2 py-1
                                       rounded hover:bg-green-200">✓ Approve</button>
                    </form>
                    <form method="POST" action="/admin/providers/{{ $p->id }}/reject">
                        @csrf
                        <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                       rounded hover:bg-red-200">✗ Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Tidak ada yang pending.</p>
        @endforelse
    </div>

    <!-- Booking Terbaru -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">📋 Booking Terbaru</h3>
            <a href="/admin/bookings" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
        </div>
        @forelse($recentBookings as $b)
            <div class="flex justify-between items-center py-3 border-b last:border-0">
                <div>
                    <p class="font-medium text-sm">{{ $b->car->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $b->user->name }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ match($b->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'approved'  => 'bg-green-100 text-green-700',
                        'rejected'  => 'bg-red-100 text-red-700',
                        'completed' => 'bg-blue-100 text-blue-700',
                        default     => 'bg-gray-100 text-gray-600',
                    } }}">
                    {{ ucfirst($b->status) }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada booking.</p>
        @endforelse
    </div>
</div>
@endsection