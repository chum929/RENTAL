<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;

class CarAdminController extends Controller {
    public function index() {
        $cars = Car::with('rentalProvider.city')->latest()->paginate(15);
        return view('admin.cars', compact('cars'));
    }

    public function destroy($id) {
        Car::findOrFail($id)->delete();
        return back()->with('success', 'Mobil dihapus.');
    }
}