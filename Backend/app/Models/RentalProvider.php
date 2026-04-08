<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalProvider extends Model {
    protected $fillable = [
        'user_id',
        'city_id',
        'business_name',
        'address',
        'phone',
        'photo',
        'status',
    ];

    // Relasi ke User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relasi ke City
    public function city() {
        return $this->belongsTo(City::class);
    }

    // Relasi ke Cars
    public function cars() {
        return $this->hasMany(Car::class);
    }

    // Relasi ke Reviews
    public function reviews() {
        return $this->hasMany(Review::class);
    }

    // Rata-rata rating
    public function averageRating() {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}