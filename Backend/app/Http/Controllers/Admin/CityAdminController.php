<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityAdminController extends Controller {
    public function index() {
        $cities = City::latest()->paginate(15);
        return view('admin.cities', compact('cities'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:100']);
        City::create(['name' => $request->name, 'province' => $request->province]);
        return back()->with('success', 'Kota ditambahkan!');
    }

    public function update(Request $request, $id) {
        $request->validate(['name' => 'required|string|max:100']);
        City::findOrFail($id)->update(['name' => $request->name, 'province' => $request->province]);
        return back()->with('success', 'Kota diperbarui!');
    }

    public function destroy($id) {
        City::findOrFail($id)->delete();
        return back()->with('success', 'Kota dihapus.');
    }
}