<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Notification;
use App\Models\RentalProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller {

    public function store(Request $request) {
        $request->validate([
            'car_id'     => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
            'notes'      => 'nullable|string',
        ]);

        $car = Car::with('rentalProvider')->findOrFail($request->car_id);

        if (!$car->isAvailableOn($request->start_date, $request->end_date)) {
            return response()->json([
                'message' => 'Mobil tidak tersedia di tanggal tersebut.'
            ], 422);
        }

        $totalDays  = Carbon::parse($request->start_date)->diffInDays($request->end_date);
        $totalPrice = $totalDays * $car->price_per_day;

        $booking = Booking::create([
            'user_id'     => $request->user()->id,
            'car_id'      => $car->id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_days'  => $totalDays,
            'total_price' => $totalPrice,
            'notes'       => $request->notes,
        ]);

        Notification::create([
            'user_id' => $car->rentalProvider->user_id,
            'title'   => 'Booking Baru!',
            'message' => "Ada booking untuk {$car->name} dari ".$request->user()->name.".",
        ]);

        return response()->json([
            'message' => 'Booking berhasil dikirim!',
            'booking' => $booking->load('car.rentalProvider'),
        ], 201);
    }

    public function myBookings(Request $request) {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['car.rentalProvider'])
            ->latest()->get();
        return response()->json($bookings);
    }

    public function show(Request $request, $id) {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['car.rentalProvider', 'review'])
            ->firstOrFail();
        return response()->json($booking);
    }

    public function cancel(Request $request, $id) {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->firstOrFail();
        $booking->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Booking dibatalkan.']);
    }

    public function ownerBookings(Request $request) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        $bookings = Booking::whereHas('car', fn($q) =>
                $q->where('rental_provider_id', $provider->id))
            ->with(['user', 'car'])
            ->latest()->get();
        return response()->json($bookings);
    }

    public function approve(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        $booking  = Booking::whereHas('car', fn($q) =>
                $q->where('rental_provider_id', $provider->id))
            ->where('id', $id)->where('status', 'pending')->firstOrFail();

        $booking->update(['status' => 'approved']);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Diterima!',
            'message' => "Booking kamu untuk {$booking->car->name} telah diterima.",
        ]);

        return response()->json(['message' => 'Booking diterima.']);
    }

    public function reject(Request $request, $id) {
        $provider = RentalProvider::where('user_id', $request->user()->id)->firstOrFail();
        $booking  = Booking::whereHas('car', fn($q) =>
                $q->where('rental_provider_id', $provider->id))
            ->where('id', $id)->where('status', 'pending')->firstOrFail();

        $booking->update(['status' => 'rejected']);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Ditolak',
            'message' => "Maaf, booking kamu untuk {$booking->car->name} ditolak.",
        ]);

        return response()->json(['message' => 'Booking ditolak.']);
    }
}