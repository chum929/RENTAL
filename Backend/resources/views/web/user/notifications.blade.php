@extends('web.layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">🔔 Notifikasi</h1>

    @forelse($notifications as $notif)
    <div class="bg-white rounded-xl border shadow-sm mb-3 p-4 flex gap-4
                {{ !$notif->is_read ? 'border-blue-200 bg-blue-50' : '' }}">
        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                    {{ !$notif->is_read ? 'bg-blue-100' : 'bg-gray-100' }}">
            <span class="text-lg">
                {{ str_contains($notif->title, 'Diterima') ? '✅'
                    : (str_contains($notif->title, 'Ditolak') ? '❌' : '🔔') }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start">
                <p class="font-semibold text-gray-800 text-sm {{ !$notif->is_read ? 'text-blue-900' : '' }}">
                    {{ $notif->title }}
                </p>
                @if(!$notif->is_read)
                <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                @endif
            </div>
            <p class="text-gray-600 text-sm mt-0.5">{{ $notif->message }}</p>
            <p class="text-gray-400 text-xs mt-1">
                {{ $notif->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border shadow-sm p-16 text-center">
        <div class="text-6xl mb-4">🔔</div>
        <p class="text-gray-500">Belum ada notifikasi.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection