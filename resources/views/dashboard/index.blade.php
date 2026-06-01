@extends('layouts.app')
@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
    {{-- Header Konten --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>

        {{-- Form Filter --}}
        <form action="{{ route('dashboard.index') }}" method="GET" class="flex items-center gap-2">
            {{-- Tombol Harian, Mingguan, Bulanan --}}
            <div class="flex bg-white border border-gray-300 rounded-md overflow-hidden">
                <button type="submit" name="filter" value="daily"
                    class="px-4 py-2 text-sm font-semibold {{ request('filter') == 'daily' ? 'bg-gray-200 text-gray-700' : 'text-gray-500 hover:bg-gray-50' }}">
                    Harian
                </button>
                <button type="submit" name="filter" value="weekly"
                    class="px-4 py-2 text-sm font-semibold {{ request('filter') == 'weekly' ? 'bg-gray-200 text-gray-700' : 'text-gray-500 hover:bg-gray-50' }}">
                    Mingguan
                </button>
                <button type="submit" name="filter" value="monthly"
                    class="px-4 py-2 text-sm font-semibold {{ request('filter') == 'monthly' ? 'bg-gray-200 text-gray-700' : 'text-gray-500 hover:bg-gray-50' }}">
                    Bulan
                </button>
            </div>

            {{-- Input Tanggal Range --}}
            <input type="date" name="start_date" value="{{ request('start_date') ?? $start }}"
                class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <span class="text-gray-500">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') ?? $end }}"
                class="border border-gray-300 rounded-md px-3 py-2 text-sm">

            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700">Filter</button>
        </form>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
        {{-- Card Total Pendapatan --}}
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500">Total Pendapatan</h3>
            <p class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        {{-- Card Transaksi --}}
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500">Jumlah Transaksi</h3>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $jumlahTransaksi }}</p>
            <h3 class="text-sm font-semibold text-gray-500 mt-3">Produk Terjual</h3>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $produkTerjual }}</p>
        </div>
    </div>

    {{-- Chart dan Stok Terendah --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chart Penjualan --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Chart Penjualan</h3>
            <div class="h-80 bg-gray-100 rounded-md flex items-center justify-center">
                <canvas id="chartPenjualan" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Stok Terendah --}}
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stok Terendah</h3>
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3">Produk</th>
                        <th scope="col" class="px-4 py-3 text-right">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokTerendah as $produk)
                        @php
                            // Default warna teks
                            $warna = 'text-gray-700';
                            
                            // Cek berdasarkan SISA STOK yang sebenarnya
                            if ($produk->sisa_stok <= 3) {
                                $warna = 'text-red-600';
                            } elseif ($produk->sisa_stok <= 5) {
                                $warna = 'text-orange-500';
                            } elseif ($produk->sisa_stok <= 10) {
                                $warna = 'text-yellow-500';
                            }
                        @endphp
                        
                        <tr class="bg-white border-b hover:bg-gray-50 transition duration-150">
                            {{-- Kolom 1: Menampilkan Nama Produk --}}
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $produk->nama_produk }}
                            </td>
                            
                            {{-- Kolom 2: Menampilkan Angka Sisa Stok & Warna --}}
                            <td class="px-4 py-3 text-right font-bold {{ $warna }}">
                                {{ $produk->sisa_stok }} {{ $produk->satuan->nama_satuan ?? '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const canvas = document.getElementById('chartPenjualan');
        if (canvas) {
            const ctx = canvas.getContext('2d');

            const labels = @json($penjualanHarian->pluck('tanggal'));
            const data = @json($penjualanHarian->pluck('total'));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Harian',
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    </script>

@endsection
