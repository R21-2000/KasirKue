<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController
{
    /**
     * Tampilkan halaman kasir beserta daftar penjualan dan detailnya.
     */
    public function kasir()
    {
        // Ambil semua produk dan hitung stok akhir langsung melalui subquery
        $produks = Produk::addSelect([
            'stok_masuk' => Stok::selectRaw('COALESCE(sum(stok), 0)')
                ->whereColumn('produk_id', 'produks.id'),
            'stok_terjual' => PenjualanDetail::selectRaw('COALESCE(sum(qty), 0)')
                ->whereColumn('produk_id', 'produks.id')
        ])->orderBy('nama_produk')->get()->map(function ($produk) {
            $produk->stok_akhir = $produk->stok_masuk - $produk->stok_terjual;
            return $produk;
        });

        return view('kasir', compact('produks'));
    }
    /**
     * Tampilkan halaman laporan transaksi penjualan.
     * Bisa difilter berdasarkan rentang tanggal.
     */
    // app/Http/Controllers/PenjualanController.php

    public function laporan(Request $request)
{
    $query = Penjualan::with(['pengguna', 'penjualanDetails.produk'])
        ->withSum('penjualanDetails as total_penjualan', 'subtotal');

    if ($request->filled('nama_pelanggan')) {
        $query->where('nama_pelanggan', 'like', '%' . $request->nama_pelanggan . '%');
    }

    if ($request->filled('metode_pembayaran')) {
        $query->where('metode_pembayaran', $request->metode_pembayaran);
    }

    if ($request->filled('dari') && $request->filled('hingga')) {
        $dari = Carbon::parse($request->dari)->startOfDay();
        $hingga = Carbon::parse($request->hingga)->endOfDay();
        $query->whereBetween('waktu_bayar', [$dari, $hingga]);
    }

    // Sorting
    $sort = $request->input('sort', 'terbaru');
    if ($sort == 'total_asc' || $sort == 'total_desc') {
        $query->orderBy('total_penjualan', $sort == 'total_asc' ? 'asc' : 'desc');
    } else {
        $query->orderBy('waktu_bayar', $sort == 'terlama' ? 'asc' : 'desc');
    }

    $penjualans = $query->get();

    // Filter total_penjualan DI COLLECTION
    if ($request->filled('minimal')) {
        $penjualans = $penjualans->filter(fn($p) => $p->total_penjualan >= $request->minimal);
    }
    if ($request->filled('maksimal')) {
        $penjualans = $penjualans->filter(fn($p) => $p->total_penjualan <= $request->maksimal);
    }

    return view('laporan.index', compact('penjualans'));
}



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'nama_pelanggan'    => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|string',
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer|exists:produks,id',
            'items.*.qty'       => 'required|integer|min:1',
            'items.*.harga'     => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->route('kasir')->withErrors($validator)->withInput();
        }

        foreach ($request->items as $item) {
            $produk = Produk::find($item['produk_id']);
            
            // Hitung sisa stok untuk produk ini
            $stokMasuk = Stok::where('produk_id', $produk->id)->sum('stok');
            $stokTerjual = PenjualanDetail::where('produk_id', $produk->id)->sum('qty');
            $stokTersedia = $stokMasuk - $stokTerjual;

            // Tolak transaksi jika qty melampaui batas
            if ($item['qty'] > $stokTersedia) {
                return redirect()->route('kasir')
                    ->withInput()
                    ->with('error', "Transaksi Gagal! Stok '{$produk->nama_produk}' tidak cukup. (Tersedia: {$stokTersedia}, Diminta: {$item['qty']})");
            }
        }

        // 2. Gunakan DB Transaction untuk memastikan semua query berhasil
        try {
            DB::beginTransaction();

            // 3. Buat record di tabel 'penjualans'
            $penjualan = Penjualan::create([
                // Buat nomor transaksi unik
                'no_transaksi' => 'TRX-' . Carbon::now()->format('YmdHis'),
                'pengguna_id' => 1,
                'nama_pelanggan' => $request->nama_pelanggan ?? 'Walk-in',
                'tanggal_penjualan' => Carbon::now()->toDateString(),
                'waktu_bayar' => Carbon::now(),
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // 4. Loop dan buat record di 'penjualan_details' untuk setiap item
            foreach ($request->items as $item) {
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id'    => $item['produk_id'],
                    'qty'          => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'subtotal'     => $item['qty'] * $item['harga'],
                ]);
            }

            DB::commit(); // Jika semua berhasil, simpan perubahan

            return redirect()->route('laporan')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('kasir')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Penjualan $penjualan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        //
    }
    /**
     * Endpoint untuk filter laporan
     */
    public function filterLaporan(Request $request)
    {
    // Logika sama seperti laporan()
    return $this->laporan($request);
    }

    public function laporanTransaksi()
    {
        // Ambil semua data penjualan, urutkan dari yang terbaru
        // Eager load relasi 'details' dan 'pengguna' untuk optimasi query
        $penjualans = Penjualan::with('details', 'pengguna')->latest()->get();

        // Kirim data ke view
        return view('laporan_transaksi', compact('penjualans'));
    }

    public function eksporPdf(Request $request)
{
    $query = Penjualan::with(['pengguna', 'penjualanDetails.produk'])
        ->withSum('penjualanDetails as total_penjualan', 'subtotal');

    if ($request->filled('nama_pelanggan')) {
        $query->where('nama_pelanggan', 'like', '%' . $request->nama_pelanggan . '%');
    }

    if ($request->filled('metode_pembayaran')) {
        $query->where('metode_pembayaran', $request->metode_pembayaran);
    }

    if ($request->filled('dari') && $request->filled('hingga')) {
        $dari = Carbon::parse($request->dari)->startOfDay();
        $hingga = Carbon::parse($request->hingga)->endOfDay();
        $query->whereBetween('waktu_bayar', [$dari, $hingga]);
    }

    // Sort sama
    $sort = $request->input('sort', 'terbaru');
    if ($sort == 'total_asc' || $sort == 'total_desc') {
        $query->orderBy('total_penjualan', $sort == 'total_asc' ? 'asc' : 'desc');
    } else {
        $query->orderBy('waktu_bayar', $sort == 'terlama' ? 'asc' : 'desc');
    }

    $penjualans = $query->get();

    // Filter total_penjualan DI PHP (jangan pakai having)
    if ($request->filled('minimal')) {
        $penjualans = $penjualans->filter(fn($p) => $p->total_penjualan >= $request->minimal);
    }
    if ($request->filled('maksimal')) {
        $penjualans = $penjualans->filter(fn($p) => $p->total_penjualan <= $request->maksimal);
    }

    $pdf = Pdf::loadView('laporan.pdf_view', compact('penjualans'));

    return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
}


}
