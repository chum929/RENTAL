@extends('web.layouts.owner')
@section('title', 'Kelola Mobil')
@section('content')

<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500 text-sm">Total: {{ $cars->total() }} mobil</p>
    <a href="{{ route('owner.cars.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm
              font-semibold hover:bg-blue-700 transition flex items-center gap-2">
        ＋ Tambah Mobil
    </a>
</div>

<div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Foto</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Nama Mobil</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Tipe</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Harga/Hari</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Kursi</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($cars as $car)
            <tr>
                <td class="px-4 py-3">
                    @if($car->photo)
                        <img src="{{ asset('storage/'.$car->photo) }}"
                             class="w-16 h-12 object-cover rounded-lg">
                    @else
                        <div class="w-16 h-12 bg-gray-100 rounded-lg flex
                                    items-center justify-center text-2xl">🚗</div>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium">{{ $car->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $car->type }}</td>
                <td class="px-4 py-3 font-semibold text-blue-700">
                    Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $car->seats }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $car->is_available
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ $car->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('owner.cars.edit', $car->id) }}"
                           class="text-xs bg-blue-100 text-blue-700 px-2 py-1
                                  rounded hover:bg-blue-200">
                            Edit
                        </a>
                        <form method="POST"
                              action="{{ route('owner.cars.destroy', $car->id) }}"
                              onsubmit="return confirm('Hapus mobil ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                           rounded hover:bg-red-200">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                    <div class="text-5xl mb-3">🚗</div>
                    <p>Belum ada mobil. Tambahkan mobil pertama kamu!</p>
                    <a href="{{ route('owner.cars.create') }}"
                       class="inline-block mt-3 bg-blue-600 text-white px-4 py-2
                              rounded-xl text-sm hover:bg-blue-700 transition">
                        Tambah Mobil
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $cars->links() }}</div>
</div>
@endsection