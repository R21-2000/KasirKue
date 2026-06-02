<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pengguna;
use App\Models\Produk;
use Carbon\Carbon;

class PenjualanManualSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data admin & produk pertama buat numpang transaksi dummy
        $pengguna = Pengguna::first();
        $produk = Produk::first();

        if (!$pengguna || !$produk) {
            $this->command->info('Gagal: Pengguna atau Produk belum ada. Pastikan sudah ada datanya.');
            return;
        }

        $hari_ini = Carbon::now();

        // Target pendapatan grafik kita selama 7 hari terakhir
        $skenario_penjualan = [
            ['hari_mundur' => 6, 'total' => 150000],
            ['hari_mundur' => 5, 'total' => 200000],
            ['hari_mundur' => 4, 'total' => 120000],
            ['hari_mundur' => 3, 'total' => 350000],
            ['hari_mundur' => 2, 'total' => 450000],
            ['hari_mundur' => 1, 'total' => 300000],
            ['hari_mundur' => 0, 'total' => 500000], // Hari ini (paling tinggi)
        ];

        foreach ($skenario_penjualan as $index => $skenario) {
            // Set waktu mundur sesuai hari
            $waktu = $hari_ini->copy()->subDays($skenario['hari_mundur'])->setTime(rand(9, 15), 0);

            // 1. Bikin kepala struknya (Tabel Penjualan)
            $penjualan = Penjualan::create([
                'no_transaksi' => 'TRX-DUMMY-' . $skenario['hari_mundur'],
                'pengguna_id' => $pengguna->id,
                'nama_pelanggan' => 'Pelanggan ' . ($index + 1),
                'tanggal_penjualan' => $waktu,
                'metode_pembayaran' => 'tunai',
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            // 2. Bikin rincian harganya biar masuk ke perhitungan grafik (Tabel PenjualanDetail)
            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'produk_id' => $produk->id,
                'qty' => 1,
                'harga_satuan' => $skenario['total'],
                'subtotal' => $skenario['total'],
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);
        }

        $this->command->info('Data manual berhasil disuntik sempurna! Silakan cek grafik di web.');
    }
}
