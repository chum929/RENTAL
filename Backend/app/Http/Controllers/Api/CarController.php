<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\RentalProvider;
use Illuminate\Http\Request;

class CarController extends Controller {

    // =============================================
    // LIST MOBIL (publik, dengan filter)
    // =============================================
    public function index(Request $request) {
        $query = Car::with(['rentalProvider.city'])
            ->where('is_available', true)
            ->whereHas('rentalProvider', fn($q) => $q->where('status', 'approved'));

        if ($request->city_id)
            $query->whereHas('rentalProvider',
                fn($q) => $q->where('city_id', $request->city_id));

        if ($request->type)
            $query->where('type', $request->type);

        if ($request->min_price)
            $query->where('price_per_day', '>=', $request->min_price);

        if ($request->max_price)
            $query->where('price_per_day', '<=', $request->max_price);

        if ($request->search)
            $query->where('name', 'like', '%'.$request->search.'%');

        return response()->json($query->paginate(10));
    }

    // =============================================
    // DETAIL SATU MOBIL (publik)
    // =============================================
    public function show($id) {
        $car = Car::with([
            'rentalProvider.city',
            'rentalProvider.reviews.user',
        ])->findOrFail($id);

        return response()->json($car);
    }

    // =============================================
    // MOBIL MILIK OWNER (perlu login owner)
    // =============================================
    public function ownerCars(Request $request) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Profil rental tidak ditemukan.'
            ], 404);
        }

        $cars = Car::where('rental_provider_id', $provider->id)
            ->latest()->get();

        return response()->json($cars);
    }

    // =============================================
    // TAMBAH MOBIL (owner) ← BAGIAN YANG DIPERBAIKI
    // =============================================
    public function store(Request $request) {
        $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|string|max:100',
            'plate_number'  => 'required|string|unique:cars,plate_number',
            'year'          => 'required|integer|min:2000|max:'.date('Y'),
            'seats'         => 'required|integer|min:2|max:20',
            'price_per_day' => 'required|numeric|min:10000',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_available'  => 'nullable|boolean',
        ]);

        // Cek apakah user punya rental provider
        $provider = RentalProvider::where('user_id', $request->user()->id)->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Kamu belum memiliki profil rental. Lengkapi profil rental dulu.'
            ], 422);
        }

        if ($provider->status !== 'approved') {
            return response()->json([
                'message' => 'Akun rental belum disetujui admin. Status: '.$provider->status
            ], 422);
        }

        $data                       = $request->except(['photo', '_token', '_method']);
        $data['rental_provider_id'] = $provider->id;
        $data['is_available']       = $request->boolean('is_available', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }

        $car = Car::create($data);

        return response()->json([
            'message' => 'Mobil berhasil ditambahkan!',
            'car'     => $car,
        ], 201);
    }

    // =============================================
    // EDIT MOBIL (owner)
    // =============================================
    public function update(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->first();

        if (!$provider) {
            return response()->json(['message' => 'Profil rental tidak ditemukan.'], 404);
        }

        $car = Car::where('id', $id)
            ->where('rental_provider_id', $provider->id)
            ->first();

        if (!$car) {
            return response()->json(['message' => 'Mobil tidak ditemukan.'], 404);
        }

        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'type'          => 'sometimes|string|max:100',
            'plate_number'  => 'sometimes|string|unique:cars,plate_number,'.$car->id,
            'year'          => 'sometimes|integer|min:2000|max:'.date('Y'),
            'seats'         => 'sometimes|integer|min:2|max:20',
            'price_per_day' => 'sometimes|numeric|min:10000',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_available'  => 'nullable',
        ]);

        $data = $request->except(['photo', '_token', '_method']);

        // Handle is_available
        if ($request->has('is_available')) {
            $data['is_available'] = filter_var(
                $request->is_available,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        // Handle upload foto baru
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }

        $car->update($data);

        return response()->json([
            'message' => 'Mobil berhasil diperbarui!',
            'car'     => $car->fresh(),
        ]);
    }

    // =============================================
    // HAPUS MOBIL (owner)
    // =============================================
    public function destroy(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->first();

        if (!$provider) {
            return response()->json(['message' => 'Profil rental tidak ditemukan.'], 404);
        }

        $car = Car::where('id', $id)
            ->where('rental_provider_id', $provider->id)
            ->first();

        if (!$car) {
            return response()->json(['message' => 'Mobil tidak ditemukan.'], 404);
        }

        $car->delete();

        return response()->json(['message' => 'Mobil berhasil dihapus.']);
    }
}