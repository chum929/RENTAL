<?php
namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use App\Models\RentalProvider;
use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Admin
        User::create([
            'name'     => 'Admin Sistem',
            'email'    => 'admin@rental.com',
            'phone'    => '08000000000',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Kota
        $cityIds = [];
        foreach (['Jakarta','Surabaya','Bandung','Bali','Yogyakarta','Malang'] as $name) {
            $cityIds[] = City::create(['name' => $name])->id;
        }

        // Owner
        $owner = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'owner@rental.com',
            'phone'    => '081234567890',
            'password' => Hash::make('owner123'),
            'role'     => 'owner',
        ]);

        $provider = RentalProvider::create([
            'user_id'       => $owner->id,
            'city_id'       => $cityIds[0],
            'business_name' => 'Budi Car Rental',
            'address'       => 'Jl. Contoh No. 123, Jakarta Selatan',
            'phone'         => '081234567890',
            'status'        => 'approved',
        ]);

        // Mobil contoh
        foreach ([
            ['Toyota Avanza',  'MPV',      350000, 7],
            ['Honda Brio',     'City Car', 280000, 5],
            ['Daihatsu Xenia', 'MPV',      320000, 7],
            ['Toyota Innova',  'MPV',      500000, 8],
            ['Honda Jazz',     'Hatchback',300000, 5],
        ] as [$name, $type, $price, $seats]) {
            Car::create([
                'rental_provider_id' => $provider->id,
                'name'               => $name,
                'type'               => $type,
                'plate_number'       => 'B '.rand(1000,9999).' '.chr(rand(65,90)).chr(rand(65,90)),
                'year'               => rand(2019, 2023),
                'seats'              => $seats,
                'price_per_day'      => $price,
                'description'        => "$name kondisi prima, AC dingin, bersih.",
                'is_available'       => true,
            ]);
        }

        // User biasa
        User::create([
            'name'     => 'Andi Penyewa',
            'email'    => 'user@rental.com',
            'phone'    => '089876543210',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);
    }
}