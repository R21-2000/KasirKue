<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari query string
        $filter = $request->filter;

        // Hitung rentang tanggal berdasarkan filter
        if ($filter === 'daily') {
            $start = Carbon::today()->toDateString();
            $end = Carbon::today()->toDateString();
        } elseif ($filter === 'weekly') {
            $start = Carbon::now()->startOfWeek()->toDateString();
            $end = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($filter === 'monthly') {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end = Carbon::now()->endOfMonth()->toDateString();
        } else {
            // Pakai input manual kalau ada
            $start = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
            $end = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();
        }

        // Ambil data penjualan dalam rentang tanggal
        $penjualan = Penjualan::whereBetween('tanggal_penjualan', [$start, $end])->get();

        // Hitung total pendapatan
        $totalPendapatan = PenjualanDetail::whereIn('penjualan_id', $penjualan->pluck('id'))
            ->sum(DB::raw('subtotal'));

        // Hitung jumlah transaksi
        $jumlahTransaksi = $penjualan->count();

        // Hitung total produk terjual
        $produkTerjual = PenjualanDetail::whereIn('penjualan_id', $penjualan->pluck('id'))
            ->sum('qty');

        // Query pendapatan harian untuk chart (group by tanggal)
        $penjualanHarian = PenjualanDetail::selectRaw('DATE(penjualans.tanggal_penjualan) as tanggal, SUM(subtotal) as total')
            ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->whereBetween('penjualans.tanggal_penjualan', [$start, $end])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Ambil stok terendah (misal top 5)
        $stokTerendah = Produk::with('satuan')
            ->addSelect([
                'total_masuk' => \App\Models\Stok::selectRaw('COALESCE(sum(stok), 0)')
                    ->whereColumn('produk_id', 'produks.id'),
                'total_terjual' => \App\Models\PenjualanDetail::selectRaw('COALESCE(sum(qty), 0)')
                    ->whereColumn('produk_id', 'produks.id')
            ])
            ->get()
            ->map(function ($produk) {
                // Hitung sisa stok nyata (stok akhir)
                $produk->sisa_stok = $produk->total_masuk - $produk->total_terjual;
                return $produk;
            })
            ->sortBy('sisa_stok') // Urutkan dari sisa stok yang paling sedikit
            ->take(5); // Ambil 5 teratas

        return view('dashboard.index', [
            'totalPendapatan' => $totalPendapatan,
            'jumlahTransaksi' => $jumlahTransaksi,
            'produkTerjual' => $produkTerjual,
            'penjualanHarian' => $penjualanHarian,
            'stokTerendah' => $stokTerendah,
            'start' => $start,
            'end' => $end
        ]);
    }
}
