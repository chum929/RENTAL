<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Notification;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller {

    public function dashboard() {
        $user = auth()->user();
        $activeBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'ongoing'])
            ->with('car.rentalProvider')->latest()->take(3)->get();
        $unreadNotif = Notification::where('user_id', $user->id)->where('is_read', false)->count();
        return view('web.user.dashboard', compact('activeBookings', 'unreadNotif'));
    }

    public function bookingForm($carId) {
        $car = Car::with('rentalProvider.city')->findOrFail($carId);
        // Ambil tanggal yang sudah dipesan (untuk disable di kalender)
        $bookedDates = Booking::where('car_id', $carId)
            ->whereIn('status', ['approved', 'ongoing'])
            ->get(['start_date', 'end_date']);
        return view('web.user.booking-form', compact('car', 'bookedDates'));
    }

    public function storeBooking(Request $request) {
        $request->validate([
            'car_id'     => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $car = Car::findOrFail($request->car_id);

        if (!$car->isAvailableOn($request->start_date, $request->end_date)) {
            return back()->withErrors(['date' => 'Mobil tidak tersedia di tanggal tersebut.']);
        }

        $totalDays  = Carbon::parse($request->start_date)->diffInDays($request->end_date);
        $totalPrice = $totalDays * $car->price_per_day;

        $booking = Booking::create([
            'user_id'     => auth()->id(),
            'car_id'      => $car->id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_days'  => $totalDays,
            'total_price' => $totalPrice,
            'notes'       => $request->notes,
        ]);

        // Notifikasi ke owner
        \App\Models\Notification::create([
            'user_id' => $car->rentalProvider->user_id,
            'title'   => 'Booking Baru!',
            'message' => "Ada permintaan booking untuk {$car->name} dari ".auth()->user()->name.".",
        ]);

        return redirect()->route('user.booking.detail', $booking->id)
            ->with('success', 'Booking berhasil dikirim! Menunggu konfirmasi owner.');
    }

    public function myBookings() {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('car.rentalProvider')
            ->latest()->paginate(10);
        return view('web.user.bookings', compact('bookings'));
    }

    public function bookingDetail($id) {
        $booking = Booking::where('user_id', auth()->id())
            ->with(['car.rentalProvider', 'review'])
            ->findOrFail($id);
        return view('web.user.booking-detail', compact('booking'));
    }

    public function cancelBooking($id) {
        $booking = Booking::where('user_id', auth()->id())
            ->where('status', 'pending')->findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking dibatalkan.');
    }

    public function notifications() {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        // Tandai semua sebagai sudah dibaca
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return view('web.user.notifications', compact('notifications'));
    }

    public function storeReview(Request $request) {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')->firstOrFail();

        if ($booking->review) {
            return back()->withErrors(['review' => 'Kamu sudah memberikan review.']);
        }

        Review::create([
            'booking_id'         => $booking->id,
            'user_id'            => auth()->id(),
            'rental_provider_id' => $booking->car->rental_provider_id,
            'rating'             => $request->rating,
            'comment'            => $request->comment,
        ]);

        return back()->with('success', 'Review berhasil dikirim!');
    }

    public function profile() {
        return view('web.user.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);
        $data = $request->only('name', 'phone');
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }
        auth()->user()->update($data);
        return back()->with('success', 'Profil diperbarui!');
    }
}