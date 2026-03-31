@extends('admin.layout')
@section('title', 'Kelola Kota')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form tambah kota -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="font-semibold mb-4">Tambah Kota</h3>
        <form method="POST" action="/admin/cities">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kota</label>
                <input type="text" name="name" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                <input type="text" name="province"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <button class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm
                           hover:bg-blue-700 transition">Tambah</button>
        </form>
    </div>

    <!-- List kota -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Kota</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Provinsi</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($cities as $city)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $city->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $city->province ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="/admin/cities/{{ $city->id }}"
                              onsubmit="return confirm('Hapus kota ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                           rounded hover:bg-red-200">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada kota.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $cities->links() }}</div>
    </div>
</div>
@endsection