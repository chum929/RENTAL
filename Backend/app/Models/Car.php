<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model {
    protected $fillable = [
        'rental_provider_id','name','type','plate_number',
        'year','seats','price_per_day','description','photo','is_available'
    ];

    protected $casts = [
        'is_available'  => 'boolean',
        'price_per_day' => 'decimal:2',
    ];

    public function rentalProvider() {
        return $this->belongsTo(RentalProvider::class);
    }
    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    public function isAvailableOn($startDate, $endDate) {
        return !$this->bookings()
            ->whereIn('status', ['approved','ongoing'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })->exists();
    }
}