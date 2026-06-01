<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelolaKasirController extends Controller
{
    public function index()
    {
        // Menampilkan semua user dengan role kasir
        $kasirs = User::where('role', 'kasir')->get();
        return view('kelola_kasir.index', compact('kasirs'));
    }

    public function create()
    {
        return view('kelola_kasir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir', // Otomatis diset sebagai kasir
        ]);

        return redirect()->route('kelola-kasir.index')->with('success', 'Akun kasir baru berhasil didaftarkan!');
    }

    public function destroy(User $kelola_kasir)
    {
        $kelola_kasir->delete();
        return redirect()->route('kelola-kasir.index')->with('success', 'Akun kasir berhasil dihapus.');
    }
}