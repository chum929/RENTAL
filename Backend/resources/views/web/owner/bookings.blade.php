@extends('web.layouts.owner')
@section('title', 'Booking Masuk')
@section('content')

<!-- Filter tab status -->
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        [null,        'Semua'],
        ['pending',   'Menunggu'],
        ['approved',  'Disetujui'],
        ['rejected',  'Ditolak'],
        ['completed', 'Selesai'],
    ] as [$val, $label])
    <a href="{{ route('owner.bookings') }}{{ $val ? '?status='.$val : '' }}"
       class="px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ request('status') === $val || (!request('status') && !$val)
                 ? 'bg-blue-600 text-white'
                 : 'bg-white border text-gray-600 hover:border-blue-400' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Penyewa</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Mobil</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Tanggal</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Total</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Catatan</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($bookings as $b)
            <tr class="{{ $b->status === 'pending' ? 'bg-yellow-50' : '' }}">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $b->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $b->user->phone }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $b->car->name }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ \Carbon\Carbon::parse($b->start_date)->format('d M Y') }}<br>
                    → {{ \Carbon\Carbon::parse($b->end_date)->format('d M Y') }}<br>
                    <span class="text-gray-400">{{ $b->total_days }} hari</span>
                </td>
                <td class="px-4 py-3 font-bold text-blue-700">
                    Rp {{ number_format($b->total_price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs">
                    {{ $b->notes ?? '-' }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ match($b->status) {
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'approved'  => 'bg-green-100 text-green-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'completed' => 'bg-blue-100 text-blue-700',
                            default     => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ ucfirst($b->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($b->status === 'pending')
                    <div class="flex gap-2">
                        <form method="POST"
                              action="{{ route('owner.booking.approve', $b->id) }}">
                            @csrf
                            <button class="text-xs bg-green-100 text-green-700
                                           px-2 py-1 rounded hover:bg-green-200
                                           font-medium">
                                ✓ Terima
                            </button>
                        </form>
                        <form method="POST"
                              action="{{ route('owner.booking.reject', $b->id) }}">
                            @csrf
                            <button class="text-xs bg-red-100 text-red-700
                                           px-2 py-1 rounded hover:bg-red-200
                                           font-medium">
                                ✗ Tolak
                            </button>
                        </form>
                    </div>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                    Belum ada booking masuk.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $bookings->links() }}</div>
</div>
@endsection