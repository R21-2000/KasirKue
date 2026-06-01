@extends('layouts.app')

@section('title', 'Stok')
@section('header_title', 'Stok')

@section('content')
    {{-- Header Konten --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800">Daftar Stok</h2>
        <a href="{{ route('stok.masuk') }}"
           class="bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-300 flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Stok
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filter dan Tabel Daftar Stok --}}
    <div class="bg-white p-6 rounded-lg shadow-sm">
        {{-- Area Filter --}}
        <form action="{{ route('stok.daftar') }}" method="GET">
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                {{-- Filter Pencarian --}}
                <div class="relative w-full md:w-1/3">
                    <input type="text" name="search" placeholder="Cari SKU atau Nama Produk..." class="w-full p-2 pl-10 border border-gray-300 rounded-md" value="{{ request('search') }}">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                {{-- Filter Tanggal --}}
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $startDate }}">
                    <span>-</span>
                    <input type="date" name="end_date" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $endDate }}">
                </div>

                {{-- Dropdown Jumlah Stok (dibuat 'range' sebagai default) --}}
                <div class="relative w-full md:w-auto">
                    <select id="jumlah_stok_filter" class="w-full p-2 border border-gray-300 rounded-md" onchange="toggleStockRangeInputs()">
                        <option value="all" {{ request()->has('min_stok') ? '' : '' }}>Semua Jumlah Stok</option>
                        <option value="range" {{ request()->has('min_stok') || !request()->query() ? 'selected' : '' }}>Rentang Jumlah Stok Akhir</option>
                    </select>
                </div>
                <button type="submit" class="bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-2 px-4 rounded-md">
                    Filter
                </button>
            </div>

            {{-- Input Rentang Jumlah Stok (dihilangkan class 'hidden' defaultnya) --}}
            <div id="stock_range_inputs" class="{{ (request()->has('min_stok') || !request()->query()) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 items-end">
                <div>
                    <label for="min_stok" class="block text-sm font-semibold text-gray-700 mb-1">MIN Stok Akhir</label>
                    <input type="number" name="min_stok" class="w-full p-2 border border-gray-300 rounded-md" placeholder="0" value="{{ request('min_stok') }}">
                </div>
                <div>
                    <label for="max_stok" class="block text-sm font-semibold text-gray-700 mb-1">MAKS Stok Akhir</label>
                    <input type="number" name="max_stok" class="w-full p-2 border border-gray-300 rounded-md" placeholder="999" value="{{ request('max_stok') }}">
                </div>
            </div>
        </form>

        {{-- Tabel Daftar Stok --}}
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3">SKU</th>
                <th scope="col" class="px-4 py-3">Nama</th>
                <th scope="col" class="px-4 py-3">Jenis</th>
                <th scope="col" class="px-4 py-3 text-right">Stok Awal</th>
                <th scope="col" class="px-4 py-3 text-right">Stok Masuk</th>
                <th scope="col" class="px-4 py-3 text-right">Stok Terjual</th>
                <th scope="col" class="px-4 py-3 text-right">Stok Akhir</th>
                <th scope="col" class="px-4 py-3">Satuan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($laporanStok as $item)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->sku }}</td>
                    <td class="px-4 py-3">{{ $item->nama_produk }}</td>
                    <td class="px-4 py-3">{{ $item->jenis }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->stok_awal }}</td>
                    <td class="px-4 py-3 text-right text-green-600">{{ $item->stok_masuk_periode }}</td>
                    <td class="px-4 py-3 text-right text-red-600">{{ $item->stok_terjual_periode }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $item->stok_akhir }}</td>
                    <td class="px-4 py-3">{{ $item->satuan->nama_satuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-10">
                        <p class="font-semibold text-gray-500">Tidak ada data stok yang ditemukan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    <script>
        function toggleStockRangeInputs() {
            const selectElement = document.getElementById('jumlah_stok_filter');
            const stockRangeInputs = document.getElementById('stock_range_inputs');
            if (selectElement.value === 'range') {
                stockRangeInputs.classList.remove('hidden');
            } else {
                stockRangeInputs.classList.add('hidden');
            }
        }
        // Panggil fungsi saat halaman pertama kali dimuat untuk memastikan kondisi filter tetap terlihat jika ada
        document.addEventListener('DOMContentLoaded', toggleStockRangeInputs);
    </script>
@endsection
