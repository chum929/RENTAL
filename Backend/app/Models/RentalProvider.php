<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalProvider extends Model {
    protected $fillable = [
        'user_id','city_id','business_name','address','phone','photo','status'
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function city()    { return $this->belongsTo(City::class); }
    public function cars()    { return $this->hasMany(Car::class); }
    public function reviews() { return $this->hasMany(Review::class); }

    public function averageRating() {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}