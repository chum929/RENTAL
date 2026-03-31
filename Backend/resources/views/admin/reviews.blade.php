@extends('admin.layout')
@section('title', 'Moderasi Review')
@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Pengguna</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Rental</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Rating</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Komentar</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($reviews as $r)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $r->user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $r->rentalProvider->business_name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $r->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                        @endfor
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $r->comment ?? '-' }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="/admin/reviews/{{ $r->id }}"
                          onsubmit="return confirm('Hapus review ini?')">
                        @csrf @method('DELETE')
                        <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                       rounded hover:bg-red-200">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada review.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $reviews->links() }}</div>
</div>
@endsection