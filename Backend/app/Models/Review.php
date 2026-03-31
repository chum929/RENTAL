<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    protected $fillable = [
        'booking_id','user_id','rental_provider_id','rating','comment'
    ];

    public function user()           { return $this->belongsTo(User::class); }
    public function booking()        { return $this->belongsTo(Booking::class); }
    public function rentalProvider() { return $this->belongsTo(RentalProvider::class); }
}