@extends('web.layouts.owner')
@section('title', $car ? 'Edit Mobil' : 'Tambah Mobil')
@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-5">
            {{ $car ? '✏️ Edit Mobil: '.$car->name : '➕ Tambah Mobil Baru' }}
        </h2>

        <form method="POST"
              action="{{ $car ? route('owner.cars.update', $car->id) : route('owner.cars.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if($car) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Mobil <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $car->name ?? '') }}"
                           placeholder="Contoh: Toyota Avanza"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select name="type"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   focus:ring-2 focus:ring-blue-500" required>
                        @foreach(['MPV','SUV','Sedan','City Car','Hatchback','Pickup','Minibus'] as $t)
                        <option value="{{ $t }}"
                            {{ old('type', $car->type ?? '') === $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Plat Nomor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Plat Nomor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="plate_number"
                           value="{{ old('plate_number', $car->plate_number ?? '') }}"
                           placeholder="B 1234 ABC"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select name="year"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   focus:ring-2 focus:ring-blue-500" required>
                        @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}"
                            {{ old('year', $car->year ?? date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endfor
                    </select>
                </div>

                <!-- Kursi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Kursi <span class="text-red-500">*</span>
                    </label>
                    <select name="seats"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   focus:ring-2 focus:ring-blue-500" required>
                        @foreach([2,4,5,6,7,8,9,10,12] as $s)
                        <option value="{{ $s }}"
                            {{ old('seats', $car->seats ?? 5) == $s ? 'selected' : '' }}>
                            {{ $s }} kursi
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga per Hari (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price_per_day"
                           value="{{ old('price_per_day', $car->price_per_day ?? '') }}"
                           placeholder="350000"
                           min="0" step="1000"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Status tersedia -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1"
                               {{ old('is_available', $car->is_available ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">
                            Mobil tersedia untuk disewa
                        </span>
                    </label>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi (opsional)
                    </label>
                    <textarea name="description" rows="3"
                        placeholder="Kondisi mobil, fasilitas tambahan, dll..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                               resize-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('description', $car->description ?? '') }}</textarea>
                </div>

                <!-- Foto -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Mobil
                    </label>
                    @if($car && $car->photo)
                    <div class="mb-3">
                        <img src="{{ asset('storage/'.$car->photo) }}"
                             class="h-32 w-auto rounded-xl object-cover">
                        <p class="text-xs text-gray-400 mt-1">
                            Upload baru untuk mengganti foto
                        </p>
                    </div>
                    @endif
                    <input type="file" name="photo" accept="image/*"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2
                                  text-sm text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">
                        Format: JPG, PNG. Maks 2MB.
                    </p>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl
                               font-semibold hover:bg-blue-700 transition text-sm">
                    {{ $car ? 'Simpan Perubahan' : 'Tambah Mobil' }}
                </button>
                <a href="{{ route('owner.cars') }}"
                   class="flex-1 text-center bg-gray-100 text-gray-700 py-2.5
                          rounded-xl font-semibold hover:bg-gray-200 transition text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection