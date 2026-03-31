<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name','email','phone','password','role','photo','is_active'
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    public function rentalProvider() {
        return $this->hasOne(RentalProvider::class);
    }
    public function bookings() {
        return $this->hasMany(Booking::class);
    }
    public function notifications() {
        return $this->hasMany(Notification::class);
    }
    public function isAdmin() { return $this->role === 'admin'; }
    public function isOwner() { return $this->role === 'owner'; }
    public function isUser()  { return $this->role === 'user';  }
}