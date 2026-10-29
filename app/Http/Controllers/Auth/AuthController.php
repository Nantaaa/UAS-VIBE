<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'beranda');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->isAdmin() ? route('admin.dashboard') : route('beranda')
        );
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('beranda');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'min:9', 'max:15', 'regex:/^[0-9+]+$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $user = User::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'password_hash' => $data['password'],
            'role' => 'wisatawan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('beranda')->with('sukses', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
