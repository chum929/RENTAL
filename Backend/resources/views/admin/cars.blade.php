@extends('admin.layout')
@section('title', 'Monitor Mobil')
@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Nama Mobil</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Tipe</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Rental</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Harga/Hari</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($cars as $car)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $car->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $car->type }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $car->rentalProvider->business_name ?? '-' }}</td>
                <td class="px-4 py-3">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs
                        {{ $car->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $car->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <form method="POST" action="/admin/cars/{{ $car->id }}"
                          onsubmit="return confirm('Hapus mobil ini?')">
                        @csrf @method('DELETE')
                        <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                       rounded hover:bg-red-200">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada mobil.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $cars->links() }}</div>
</div>
@endsection