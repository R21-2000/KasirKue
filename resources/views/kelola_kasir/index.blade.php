@extends('layouts.app')

@section('title', 'Kelola Kasir')
@section('header_title', 'Kelola Kasir')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800">Manajemen Akun Kasir</h2>
        <a href="{{ route('kelola-kasir.create') }}"
           class="bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-300 flex items-center">
            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Kasir Baru
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Nama Pegawai</th>
                    <th class="px-4 py-3">Email Login</th>
                    <th class="px-4 py-3">Hak Akses / Peran</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kasirs as $kasir)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $kasir->name }}</td>
                        <td class="px-4 py-3">{{ $kasir->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full uppercase">
                                {{ $kasir->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('kelola-kasir.destroy', $kasir->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kasir ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus Akun
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 text-gray-500 font-medium">Belum ada akun kasir yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection