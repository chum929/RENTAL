<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Notification;
use App\Models\RentalProvider;
use Illuminate\Http\Request;

class OwnerWebController extends Controller {

    private function getProvider() {
        return RentalProvider::where('user_id', auth()->id())->firstOrFail();
    }

    public function dashboard() {
        $provider = $this->getProvider();
        $totalCars      = Car::where('rental_provider_id', $provider->id)->count();
        $pendingBookings = Booking::whereHas('car', fn($q) => $q->where('rental_provider_id', $provider->id))
            ->where('status', 'pending')->count();
        $recentBookings = Booking::whereHas('car', fn($q) => $q->where('rental_provider_id', $provider->id))
            ->with(['user', 'car'])->latest()->take(5)->get();
        return view('web.owner.dashboard', compact('provider', 'totalCars', 'pendingBookings', 'recentBookings'));
    }

    public function cars() {
        $provider = $this->getProvider();
        $cars = Car::where('rental_provider_id', $provider->id)->latest()->paginate(10);
        return view('web.owner.cars', compact('cars', 'provider'));
    }

    public function createCar() {
        return view('web.owner.car-form', ['car' => null]);
    }

    public function storeCar(Request $request) {
        $request->validate([
            'name'          => 'required|string',
            'type'          => 'required|string',
            'plate_number'  => 'required|string|unique:cars',
            'year'          => 'required|integer',
            'seats'         => 'required|integer|min:2',
            'price_per_day' => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|max:2048',
        ]);

        $provider = $this->getProvider();
        $data = $request->except('photo');
        $data['rental_provider_id'] = $provider->id;
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }
        Car::create($data);
        return redirect()->route('owner.cars')->with('success', 'Mobil berhasil ditambahkan!');
    }

    public function editCar($id) {
        $provider = $this->getProvider();
        $car = Car::where('id', $id)->where('rental_provider_id', $provider->id)->firstOrFail();
        return view('web.owner.car-form', compact('car'));
    }

    public function updateCar(Request $request, $id) {
        $provider = $this->getProvider();
        $car = Car::where('id', $id)->where('rental_provider_id', $provider->id)->firstOrFail();
        $data = $request->except(['photo', '_method', '_token']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('cars', 'public');
        }
        $car->update($data);
        return redirect()->route('owner.cars')->with('success', 'Mobil diperbarui!');
    }

    public function destroyCar($id) {
        $provider = $this->getProvider();
        Car::where('id', $id)->where('rental_provider_id', $provider->id)->firstOrFail()->delete();
        return back()->with('success', 'Mobil dihapus.');
    }

    public function bookings() {
    $provider = $this->getProvider();
    $query = Booking::whereHas('car',
        fn($q) => $q->where('rental_provider_id', $provider->id))
        ->with(['user', 'car']);

    // Filter berdasarkan status jika ada
    if (request('status')) {
        $query->where('status', request('status'));
    }

    $bookings = $query->latest()->paginate(15);
    return view('web.owner.bookings', compact('bookings'));
}

    public function approve($id) {
        $provider = $this->getProvider();
        $booking  = Booking::whereHas('car', fn($q) => $q->where('rental_provider_id', $provider->id))
            ->where('id', $id)->where('status', 'pending')->firstOrFail();
        $booking->update(['status' => 'approved']);
        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Diterima!',
            'message' => "Booking kamu untuk {$booking->car->name} telah diterima.",
        ]);
        return back()->with('success', 'Booking diterima!');
    }

    public function reject($id) {
        $provider = $this->getProvider();
        $booking  = Booking::whereHas('car', fn($q) => $q->where('rental_provider_id', $provider->id))
            ->where('id', $id)->where('status', 'pending')->firstOrFail();
        $booking->update(['status' => 'rejected']);
        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Ditolak',
            'message' => "Maaf, booking kamu untuk {$booking->car->name} ditolak.",
        ]);
        return back()->with('success', 'Booking ditolak.');
    }

    public function profile() {
        $provider = $this->getProvider();
        return view('web.owner.profile', compact('provider'));
    }

    public function updateProfile(Request $request) {
        $provider = $this->getProvider();
        $request->validate([
            'business_name' => 'required|string',
            'address'       => 'required|string',
            'phone'         => 'required|string',
            'photo'         => 'nullable|image|max:2048',
        ]);
        $data = $request->only('business_name', 'address', 'phone');
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('providers', 'public');
        }
        $provider->update($data);
        return back()->with('success', 'Profil rental diperbarui!');
    }
    
}