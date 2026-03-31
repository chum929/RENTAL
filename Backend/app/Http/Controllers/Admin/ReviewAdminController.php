<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewAdminController extends Controller {
    public function index() {
        $reviews = Review::with(['user','rentalProvider'])->latest()->paginate(15);
        return view('admin.reviews', compact('reviews'));
    }

    public function destroy($id) {
        Review::findOrFail($id)->delete();
        return back()->with('success', 'Review dihapus.');
    }
}