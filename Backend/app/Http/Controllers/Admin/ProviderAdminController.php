<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalProvider;

class ProviderAdminController extends Controller {
    public function index() {
        $providers = RentalProvider::with(['user','city'])->latest()->paginate(15);
        return view('admin.providers', compact('providers'));
    }

    public function approve($id) {
        RentalProvider::findOrFail($id)->update(['status' => 'approved']);
        return back()->with('success', 'Penyedia disetujui!');
    }

    public function reject($id) {
        RentalProvider::findOrFail($id)->update(['status' => 'rejected']);
        return back()->with('success', 'Penyedia ditolak.');
    }
}
