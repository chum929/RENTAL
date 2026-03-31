<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller {
    public function store(Request $request) {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->firstOrFail();

        if ($booking->review) {
            return response()->json(['message' => 'Sudah memberikan review.'], 422);
        }

        $review = Review::create([
            'booking_id'         => $booking->id,
            'user_id'            => $request->user()->id,
            'rental_provider_id' => $booking->car->rental_provider_id,
            'rating'             => $request->rating,
            'comment'            => $request->comment,
        ]);

        return response()->json(['message' => 'Review dikirim!', 'review' => $review], 201);
    }
}