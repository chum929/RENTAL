<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingAdminController extends Controller {
    public function index() {
        $bookings = Booking::with(['user','car.rentalProvider'])->latest()->paginate(15);
        return view('admin.bookings', compact('bookings'));
    }
}