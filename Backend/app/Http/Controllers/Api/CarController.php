<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\RentalProvider;
use Illuminate\Http\Request;

class CarController extends Controller {

    public function index(Request $request) {
        $query = Car::with(['rentalProvider.city'])
            ->where('is_available', true)
            ->whereHas('rentalProvider', fn($q) => $q->where('status', 'approved'));

        if ($request->city_id)  $query->whereHas('rentalProvider', fn($q) => $q->where('city_id', $request->city_id));
        if ($request->type)     $query->where('type', $request->type);
        if ($request->min_price)$query->where('price_per_day', '>=', $request->min_price);
        if ($request->max_price)$query->where('price_per_day', '<=', $request->max_price);
        if ($request->search)   $query->where('name', 'like', '%'.$request->search.'%');

        return response()->json($query->paginate(10));
    }

    public function show($id) {
        $car = Car::with(['rentalProvider.city', 'rentalProvider.reviews.user'])
            ->findOrFail($id);
        return response()->json($car);
    }

    public function ownerCars(Request $request) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        return response()->json(Car::where('rental_provider_id', $provider->id)->get());
    }

    public function store(Request $request) {
        $request->validate([
            'name'          => 'required|string',
            'type'          => 'required|string',
            'plate_number'  => 'required|string|unique:cars',
            'year'          => 'required|integer',
            'seats'         => 'required|integer|min:2',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        $data = $request->except('photo');
        $data['rental_provider_id'] = $provider->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }

        $car = Car::create($data);
        return response()->json(['message' => 'Mobil ditambahkan!', 'car' => $car], 201);
    }

    public function update(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        $car = Car::where('id', $id)
            ->where('rental_provider_id', $provider->id)
            ->firstOrFail();

        $data = $request->except(['photo','_method','_token']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }
        $car->update($data);
        return response()->json(['message' => 'Mobil diperbarui!', 'car' => $car]);
    }

    public function destroy(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        Car::where('id', $id)
            ->where('rental_provider_id', $provider->id)
            ->firstOrFail()
            ->delete();
        return response()->json(['message' => 'Mobil dihapus.']);
    }
}