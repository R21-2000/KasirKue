<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // BARU: Menampilkan form edit profil
    public function showProfileForm()
    {
        return view('auth.edit', ['user' => Auth::user()]);
    }

    // BARU: Memproses update profil
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('profile')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect('profile')->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * [BARU] Menghapus akun pengguna yang sedang login.
     */
    public function destroyProfile(Request $request)
    {
        // Ambil pengguna yang sedang terautentikasi
        $user = Auth::user();

        // Lakukan logout terlebih dahulu
        Auth::logout();

        // Hapus data pengguna dari database
        if ($user->delete()) {
            // Hancurkan session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Arahkan ke halaman login dengan pesan sukses
            return redirect('/login')->with('status', 'Akun Anda telah berhasil dihapus secara permanen.');
        }

        // Jika gagal, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->with('error', 'Terjadi kesalahan saat mencoba menghapus akun Anda.');
    }
}