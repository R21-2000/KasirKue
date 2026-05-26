@extends('layouts.app')

@section('title', 'Tambah Stok')
@section('header_title', 'Tambah Stok')

@section('content')
    {{-- Header Konten --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800">Tambah Stok</h2>
        {{-- Tombol untuk kembali ke halaman stok masuk --}}
        <a href="{{ route('stok.masuk') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times text-2xl"></i>
        </a>
    </div>

    {{-- Form Tambah Stok --}}
    {{-- Formulir ini akan mengirimkan data ke route 'stok.store' menggunakan metode POST --}}
    <form action="{{ route('stok.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm">
        @csrf {{-- Token CSRF untuk keamanan formulir Laravel --}}

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Dropdown untuk memilih produk --}}
            <div class="md:col-span-2">
                <label for="produk_id" class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk</label>
                <select name="produk_id" id="produk_id" class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" required>
                    <option value="">Pilih Produk</option>
                    {{-- Loop melalui variabel $produks yang dikirim dari StokController --}}
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }} ({{ $produk->satuan->nama_satuan }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Input untuk jumlah stok yang akan ditambahkan --}}
            <div class="md:col-span-2">
                <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-2">Stok Tambah</label>
                {{-- Atribut 'name' diubah menjadi 'jumlah' agar sesuai dengan validasi di controller --}}
                <input type="number" name="jumlah" id="jumlah" class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" placeholder="Contoh: 10" required>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end gap-4 mt-6 pt-4 border-t">
            {{-- Tombol Batal akan mengarahkan kembali ke halaman stok masuk --}}
            <a href="{{ route('stok.masuk') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-300">
                Batal
            </a>
            {{-- Tombol Simpan akan mengirimkan formulir --}}
            <button type="submit" class="bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-300">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection