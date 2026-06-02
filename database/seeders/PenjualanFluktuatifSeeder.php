<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pengguna;
use App\Models\Produk;
use Carbon\Carbon;

class PenjualanFluktuatifSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = Pengguna::first();
        $produks = Produk::all();

        // Jaga-jaga kalau lu belum input produk di web
        if (!$pengguna || $produks->count() == 0) {
            $this->command->info('Gagal: Tambahin dulu minimal 2-3 produk dari web KasirKue lu ya, Kha!');
            return;
        }

        $hari_ini = Carbon::now();
        $trx_count = 1;

        // Bikin grafik naik turun selama 30 hari ke belakang
        for ($i = 30; $i >= 0; $i--) {
            // Fluktuasi acak: kadang 1 transaksi, kadang 6 transaksi sehari
            $jumlah_transaksi_per_hari = rand(1, 6);

            // Trik khusus: Bikin penjualan membludak di hari ini dan 3 hari lalu biar grafiknya punya "Puncak" yang bagus buat dipamerin
            if ($i == 3 || $i == 0) {
                $jumlah_transaksi_per_hari = rand(8, 15);
            }

            for ($j = 0; $j < $jumlah_transaksi_per_hari; $j++) {
                // Acak jam transaksi (jam 8 pagi sampe 8 malem)
                $waktu = $hari_ini->copy()->subDays($i)->setTime(rand(8, 20), rand(0, 59));

                // 1. Bikin Nota Struk
                $penjualan = Penjualan::create([
                    'no_transaksi' => 'TRX-' . $waktu->format('Ymd') . '-' . str_pad($trx_count, 3, '0', STR_PAD_LEFT),
                    'pengguna_id' => $pengguna->id,
                    'nama_pelanggan' => 'Pelanggan Walk-in',
                    'tanggal_penjualan' => $waktu,
                    'metode_pembayaran' => 'tunai',
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                // 2. Acak barang dari database lu (tiap pelanggan beli 1-3 macam barang)
                $macam_barang = rand(1, 3);
                $produk_dibeli = $produks->random(min($macam_barang, $produks->count()));

                foreach ($produk_dibeli as $produk) {
                    $qty = rand(1, 4); // Pelanggan beli 1 sampai 4 pcs

                    // Kita patok harga dummy aja 10rb - 35rb biar aman (kalau misalnya tabel lu beda nama kolom)
                    $harga_satuan = rand(10, 35) * 1000;

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'produk_id' => $produk->id,
                        'qty' => $qty,
                        'harga_satuan' => $harga_satuan,
                        'subtotal' => $qty * $harga_satuan,
                        'created_at' => $waktu,
                        'updated_at' => $waktu,
                    ]);
                }
                $trx_count++;
            }
        }

        $this->command->info('Data Penjualan Fluktuatif 30 Hari Sukses! Silakan cek grafik.');
    }
}
