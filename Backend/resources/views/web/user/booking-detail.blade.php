@extends('web.layouts.app')
@section('title', 'Detail Booking #' . $booking->id)
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('user.bookings') }}" class="hover:text-blue-600">
            Booking Saya
        </a>
        <span class="mx-2">›</span>
        <span class="text-gray-800">Detail #{{ $booking->id }}</span>
    </nav>

    <!-- Status besar -->
    @php
        $statusConfig = match($booking->status) {
            'pending'   => ['bg-yellow-50 border-yellow-200', 'text-yellow-700', '⏳', 'Menunggu Konfirmasi', 'Booking kamu sedang menunggu persetujuan dari penyedia rental.'],
            'approved'  => ['bg-green-50 border-green-200',  'text-green-700',  '✅', 'Booking Disetujui',   'Booking kamu telah disetujui! Silakan ambil mobil sesuai tanggal.'],
            'rejected'  => ['bg-red-50 border-red-200',      'text-red-700',    '❌', 'Booking Ditolak',     'Maaf, booking kamu ditolak oleh penyedia rental.'],
            'completed' => ['bg-blue-50 border-blue-200',    'text-blue-700',   '🎉', 'Booking Selesai',     'Terima kasih telah menggunakan layanan kami!'],
            'cancelled' => ['bg-gray-50 border-gray-200',    'text-gray-600',   '🚫', 'Dibatalkan',          'Booking ini telah dibatalkan.'],
            default     => ['bg-gray-50 border-gray-200',    'text-gray-600',   '📋', ucfirst($booking->status), ''],
        };
    @endphp

    <div class="border rounded-2xl p-6 text-center mb-6 {{ $statusConfig[0] }}">
        <div class="text-5xl mb-3">{{ $statusConfig[2] }}</div>
        <h2 class="text-xl font-bold {{ $statusConfig[1] }}">{{ $statusConfig[3] }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $statusConfig[4] }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Info Mobil -->
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b">🚗 Info Mobil</h3>
            @if($booking->car && $booking->car->photo)
                <img src="{{ asset('storage/'.$booking->car->photo) }}"
                     class="w-full h-40 object-cover rounded-xl mb-4">
            @else
                <div class="w-full h-40 bg-gray-100 rounded-xl flex items-center
                            justify-center text-6xl mb-4">🚗</div>
            @endif
            <p class="font-bold text-lg">{{ $booking->car->name ?? '-' }}</p>
            <p class="text-sm text-gray-500">
                {{ $booking->car->type ?? '' }} · {{ $booking->car->seats ?? '' }} kursi
            </p>
            <p class="text-sm text-gray-500 mt-1">
                🏢 {{ $booking->car->rentalProvider->business_name ?? '-' }}
            </p>
        </div>

        <!-- Info Booking -->
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b">📋 Rincian Booking</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">ID Booking</span>
                    <span class="font-medium">#{{ $booking->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Mulai</span>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Selesai</span>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Durasi</span>
                    <span class="font-medium">{{ $booking->total_days }} hari</span>
                </div>
                <div class="border-t pt-3 flex justify-between">
                    <span class="text-gray-500">Harga per Hari</span>
                    <span class="font-medium">
                        Rp {{ number_format($booking->car->price_per_day ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-base font-bold text-blue-700">
                    <span>Total Harga</span>
                    <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($booking->notes)
            <div class="mt-4 pt-3 border-t">
                <p class="text-xs text-gray-400 mb-1">Catatan</p>
                <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Aksi -->
    <div class="mt-6 flex gap-3">
        @if($booking->status === 'pending')
        <form method="POST" action="{{ route('user.booking.cancel', $booking->id) }}"
              onsubmit="return confirm('Batalkan booking ini?')">
            @csrf
            <button class="px-6 py-2.5 border-2 border-red-500 text-red-600 rounded-xl
                           font-semibold hover:bg-red-50 transition text-sm">
                🚫 Batalkan Booking
            </button>
        </form>
        @endif

        @if($booking->status === 'completed' && !$booking->review)
        <a href="#review-form"
           class="px-6 py-2.5 bg-yellow-400 text-yellow-900 rounded-xl
                  font-semibold hover:bg-yellow-500 transition text-sm">
            ⭐ Beri Review
        </a>
        @endif

        <a href="{{ route('user.bookings') }}"
           class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl
                  font-semibold hover:bg-gray-200 transition text-sm">
            ← Kembali
        </a>
    </div>

    <!-- Review form jika sudah selesai -->
    @if($booking->status === 'completed' && !$booking->review)
    <div id="review-form" class="mt-8 bg-white rounded-2xl border shadow-sm p-6">
        <h3 class="font-semibold text-lg mb-4">⭐ Tulis Review</h3>
        <form method="POST" action="{{ route('user.reviews.store') }}">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            <!-- Rating bintang -->
            <div class="mb-4" x-data="{ rating: 5 }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}"
                               class="sr-only" {{ $i == 5 ? 'checked' : '' }}>
                        <span class="text-3xl hover:scale-110 transition-transform
                                     inline-block select-none">⭐</span>
                    </label>
                    @endfor
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Komentar (opsional)
                </label>
                <textarea name="comment" rows="3"
                    placeholder="Ceritakan pengalamanmu menyewa di sini..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent
                           resize-none text-sm"></textarea>
            </div>

            <button type="submit"
                    class="bg-yellow-400 text-yellow-900 px-6 py-2.5 rounded-xl
                           font-semibold hover:bg-yellow-500 transition text-sm">
                Kirim Review
            </button>
        </form>
    </div>
    @endif

    @if($booking->review)
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
        <h3 class="font-semibold text-gray-800 mb-2">⭐ Review Kamu</h3>
        <div class="flex gap-1 mb-2">
            @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $booking->review->rating
                    ? 'text-yellow-400' : 'text-gray-300' }} text-xl">★</span>
            @endfor
        </div>
        <p class="text-gray-700 text-sm">{{ $booking->review->comment ?? 'Tidak ada komentar.' }}</p>
    </div>
    @endif
</div>
@endsection