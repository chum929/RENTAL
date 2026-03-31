@extends('web.layouts.app')
@section('title', 'Booking ' . $car->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Form Booking</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- FORM -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h2 class="font-semibold text-lg mb-4">Pilih Tanggal Sewa</h2>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-4">
                        @foreach($errors->all() as $error)
                            <p class="text-sm">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('user.booking.store') }}" id="bookingForm">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}">

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Tanggal Mulai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('start_date') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                        <!-- Tanggal Selesai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Selesai
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                   value="{{ old('end_date') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                    </div>

                    <!-- Perhitungan harga otomatis -->
                    <div id="price-preview" class="hidden bg-blue-50 rounded-xl p-4 mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Durasi</span>
                            <span id="total-days">-</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Harga per hari</span>
                            <span>Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-blue-800 border-t border-blue-200 pt-2 mt-2">
                            <span>Total Harga</span>
                            <span id="total-price">-</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan (opsional)
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="Misal: butuh kursi anak, jemput di mana, dll..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                         focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                         resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold
                                   hover:bg-blue-700 transition text-lg">
                        ✅ Kirim Booking
                    </button>
                </form>
            </div>
        </div>

        <!-- RINGKASAN MOBIL -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl border shadow-sm p-5 sticky top-20">
                <h3 class="font-semibold mb-3">Ringkasan Pesanan</h3>
                @if($car->photo)
                    <img src="{{ asset('storage/'.$car->photo) }}"
                         class="w-full h-36 object-cover rounded-xl mb-3">
                @else
                    <div class="w-full h-36 bg-gray-100 rounded-xl flex items-center
                                justify-center text-5xl mb-3">🚗</div>
                @endif
                <h4 class="font-bold text-gray-800">{{ $car->name }}</h4>
                <p class="text-sm text-gray-500 mb-1">{{ $car->type }} · {{ $car->seats }} kursi</p>
                <p class="text-sm text-gray-500 mb-3">
                    🏢 {{ $car->rentalProvider->business_name ?? '-' }}
                </p>
                <p class="text-sm text-gray-500">
                    📍 {{ $car->rentalProvider->city->name ?? '-' }}
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Hitung harga otomatis saat tanggal dipilih
const pricePerDay = {{ $car->price_per_day }};
const startInput  = document.getElementById('start_date');
const endInput    = document.getElementById('end_date');

function updatePrice() {
    const start = new Date(startInput.value);
    const end   = new Date(endInput.value);

    if (startInput.value && endInput.value && end > start) {
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        const total = days * pricePerDay;

        document.getElementById('total-days').textContent = days + ' hari';
        document.getElementById('total-price').textContent =
            'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('price-preview').classList.remove('hidden');

        // Set min end_date = start_date + 1
        const minEnd = new Date(start);
        minEnd.setDate(minEnd.getDate() + 1);
        endInput.min = minEnd.toISOString().split('T')[0];
    }
}

startInput.addEventListener('change', updatePrice);
endInput.addEventListener('change', updatePrice);
</script>
@endpush
@endsection