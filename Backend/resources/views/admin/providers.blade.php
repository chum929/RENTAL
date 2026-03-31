@extends('admin.layout')
@section('title', 'Kelola Penyedia Rental')
@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Nama Usaha</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Pemilik</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Kota</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($providers as $p)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $p->business_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->city->name }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ match($p->status) {
                            'approved' => 'bg-green-100 text-green-700',
                            'pending'  => 'bg-yellow-100 text-yellow-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            default    => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        @if($p->status !== 'approved')
                        <form method="POST" action="/admin/providers/{{ $p->id }}/approve">
                            @csrf
                            <button class="text-xs bg-green-100 text-green-700 px-2 py-1
                                           rounded hover:bg-green-200">✓ Approve</button>
                        </form>
                        @endif
                        @if($p->status !== 'rejected')
                        <form method="POST" action="/admin/providers/{{ $p->id }}/reject">
                            @csrf
                            <button class="text-xs bg-red-100 text-red-700 px-2 py-1
                                           rounded hover:bg-red-200">✗ Tolak</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada penyedia.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $providers->links() }}</div>
</div>
@endsection