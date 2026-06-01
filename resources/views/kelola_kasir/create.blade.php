@extends('layouts.app')

@section('title', 'Tambah Kasir')
@section('header_title', 'Tambah Kasir')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Registrasi Akun Kasir Baru</h2>
        <a href="{{ route('kelola-kasir.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times text-2xl"></i>
        </a>
    </div>

    <form action="{{ route('kelola-kasir.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm max-w-2xl">
        @csrf

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p class="font-bold">Terjadi Kesalahan Validasi:</p>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Pegawai</label>
            <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Nama Lengkap Kasir" value="{{ old('name') }}" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Kredensial Login</label>
            <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded-md" placeholder="contoh: kasir1@stoku.com" value="{{ old('email') }}" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi (Min. 8 Karakter)</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('kelola-kasir.index') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-md transition shadow-md">
                Batal
            </a>
            <button type="submit" class="bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-2 px-6 rounded-md transition shadow-md">
                Daftarkan Kasir
            </button>
        </div>
    </form>
@endsection