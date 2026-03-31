<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserAdminController extends Controller {
    public function index() {
        $users = User::whereIn('role', ['user','owner'])->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function block($id) {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'diblokir';
        return back()->with('success', "Akun berhasil $status.");
    }
}