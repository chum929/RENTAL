<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\City;
use App\Models\RentalProvider;
use Illuminate\Http\Request;

class HomeController extends Controller {

    // Halaman beranda
    public function index() {
        $featuredCars = Car::with('rentalProvider.city')
            ->where('is_available', true)
            ->whereHas('rentalProvider', fn($q) => $q->where('status', 'approved'))
            ->latest()->take(6)->get();

        $cities = City::where('is_active', true)->get();
        $totalCars = Car::count();
        $totalProviders = RentalProvider::where('status', 'approved')->count();

        return view('web.home', compact('featuredCars', 'cities', 'totalCars', 'totalProviders'));
    }

    // Halaman daftar mobil (dengan filter & search)
    public function cars(Request $request) {
        $query = Car::with(['rentalProvider.city'])
            ->where('is_available', true)
            ->whereHas('rentalProvider', fn($q) => $q->where('status', 'approved'));

        if ($request->city_id)  $query->whereHas('rentalProvider', fn($q) => $q->where('city_id', $request->city_id));
        if ($request->type)     $query->where('type', $request->type);
        if ($request->min_price)$query->where('price_per_day', '>=', $request->min_price);
        if ($request->max_price)$query->where('price_per_day', '<=', $request->max_price);
        if ($request->search)   $query->where('name', 'like', '%'.$request->search.'%');

        $cars  = $query->paginate(12)->withQueryString();
        $cities = City::where('is_active', true)->get();
        $types  = Car::distinct()->pluck('type');

        return view('web.cars.index', compact('cars', 'cities', 'types'));
    }

    // Halaman detail satu mobil
    public function carDetail($id) {
        $car = Car::with([
            'rentalProvider.city',
            'rentalProvider.reviews.user',
        ])->findOrFail($id);

        $avgRating = $car->rentalProvider->reviews()->avg('rating');
        $totalReviews = $car->rentalProvider->reviews()->count();

        return view('web.cars.show', compact('car', 'avgRating', 'totalReviews'));
    }
}