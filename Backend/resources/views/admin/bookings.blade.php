@extends('admin.layout')
@section('title', 'Monitor Booking')
@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Penyewa</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Mobil</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Tanggal</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Total</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($bookings as $b)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $b->user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $b->car->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ \Carbon\Carbon::parse($b->start_date)->format('d M Y') }} -
                    {{ \Carbon\Carbon::parse($b->end_date)->format('d M Y') }}
                </td>
                <td class="px-4 py-3 font-semibold text-blue-700">
                    Rp {{ number_format($b->total_price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ match($b->status) {
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'approved'  => 'bg-green-100 text-green-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'completed' => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-gray-100 text-gray-600',
                            default     => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ ucfirst($b->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $bookings->links() }}</div>
</div>
@endsection