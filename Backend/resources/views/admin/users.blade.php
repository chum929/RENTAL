@extends('admin.layout')
@section('title', 'Kelola User')
@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Nama</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Email</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Role</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $u)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs
                        {{ $u->role === 'owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($u->role) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs
                        {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $u->is_active ? 'Aktif' : 'Diblokir' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <form method="POST" action="/admin/users/{{ $u->id }}/block">
                        @csrf
                        <button class="text-xs {{ $u->is_active
                            ? 'bg-red-100 text-red-700 hover:bg-red-200'
                            : 'bg-green-100 text-green-700 hover:bg-green-200' }}
                            px-2 py-1 rounded">
                            {{ $u->is_active ? 'Blokir' : 'Aktifkan' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection