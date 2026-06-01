@extends('layouts.app')

@section('title', 'Kasir')
@section('header_title', 'Kasir')

@section('content')
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Formulir Kasir --}}
    <form action="{{ route('kasir.store') }}" method="POST" id="kasir-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Kolom Kiri: Keranjang Belanja & Pencarian --}}
            <div class="lg:col-span-3 bg-white p-6 rounded-lg shadow-sm">
                
                {{-- [UBAHAN] Area Pencarian dan Dropdown --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Opsi 1: Pencarian Produk --}}
                    <div class="relative">
                        <label for="search-produk" class="block text-sm font-semibold text-gray-700 mb-2">Cari Produk (Nama / SKU)</label>
                        <input type="text" id="search-produk" class="w-full p-2 pl-10 border border-gray-300 rounded-md" placeholder="Ketik untuk mencari...">
                        <i class="fa-solid fa-search absolute left-3 top-10 text-gray-400"></i>
                        <div id="search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 shadow-lg hidden"></div>
                    </div>
                    
                    {{-- [BARU] Opsi 2: Dropdown Produk --}}
                    <div class="relative">
                        <label for="select-produk" class="block text-sm font-semibold text-gray-700 mb-2">Atau Pilih Langsung</label>
                        <select id="select-produk" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produks as $produk)
                                {{-- Sembunyikan produk jika stoknya sudah habis --}}
                                @if($produk->stok_akhir > 0)
                                    <option value="{{ $produk->id }}" 
                                            data-nama="{{ $produk->nama_produk }}" 
                                            data-harga="{{ $produk->harga_satuan }}"
                                            data-stok="{{ $produk->stok_akhir }}">
                                        {{ $produk->nama_produk }} (Sisa: {{ $produk->stok_akhir }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tabel Keranjang Belanja --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th class="py-2">Produk</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Subtotal</th>
                                <th class="py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                           <tr id="placeholder-row">
                               <td colspan="4" class="text-center py-10 text-gray-400">Keranjang masih kosong</td>
                           </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kolom Kanan: Detail Transaksi --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm h-fit">
                <div class="mb-4">
                    <label for="nama_pelanggan" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Walk-in Customer">
                </div>

                <div class="mb-4">
                    <label for="metode_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="w-full p-2 border border-gray-300 rounded-md" required>
                        <option value="Tunai">Tunai</option>
                        <option value="Kartu Debit/Kredit">Kartu Debit/Kredit</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Total</span>
                        <span id="total-text" class="font-bold text-lg text-gray-800">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-orange hover:bg-brand-orange-dark text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-300 mt-4">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Transaksi
                </button>
            </div>
        </div>
    </form>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-produk');
    const searchResults = document.getElementById('search-results');
    const selectProduk = document.getElementById('select-produk'); // [BARU]
    const cartItems = document.getElementById('cart-items');
    const totalText = document.getElementById('total-text');
    const kasirForm = document.getElementById('kasir-form');
    const placeholderRow = document.getElementById('placeholder-row');
    let cart = {};

    const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

    // --- LOGIKA PENCARIAN PRODUK ---
    searchInput.addEventListener('keyup', async function(e) {
        // ... (kode pencarian lama, tidak perlu diubah)
    });

    // --- [BARU] LOGIKA DROPDOWN ---
    selectProduk.addEventListener('change', function(e) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        if (!selectedOption.value) return; // Abaikan jika memilih "-- Pilih Produk --"

        // Ambil data dari atribut data-*
        const produk = {
            id: selectedOption.value,
            nama_produk: selectedOption.dataset.nama,
            harga_satuan: selectedOption.dataset.harga,
            stok_maks: parseInt(selectedOption.dataset.stok) // Ambil nilai stok
        };
        
        addProductToCart(produk);
        
        // Reset dropdown kembali ke pilihan default
        e.target.value = ''; 
    });


    // Fungsi addProductToCart (sedikit disesuaikan agar menerima data dari kedua sumber)
    const addProductToCart = (produk) => {
        const produkId = produk.id;
        if (cart[produkId]) {
            // Cek sebelum ditambah
            if (cart[produkId].qty < cart[produkId].stok_maks) {
                cart[produkId].qty++;
            } else {
                alert(`Maksimal stok untuk ${produk.nama_produk} adalah ${produk.stok_maks}`);
            }
        } else {
            cart[produkId] = {
                id: produkId,
                nama: produk.nama_produk,
                harga: parseFloat(produk.harga_satuan),
                qty: 1,
                stok_maks: produk.stok_maks // Simpan batas stok di keranjang
            };
        }
        searchInput.value = '';
        if(searchResults) searchResults.classList.add('hidden');
        renderCart();
    };

    // ... sisa kode JavaScript lainnya (renderCart, event listener, dll tidak perlu diubah) ...
    const renderCart = () => {
        cartItems.innerHTML = ''; // Kosongkan tabel
        let total = 0;

        if (Object.keys(cart).length === 0 && placeholderRow) {
            cartItems.appendChild(placeholderRow);
        } else {
             if(placeholderRow) placeholderRow.style.display = 'none';
        }

        for (const id in cart) {
            const item = cart[id];
            const subtotal = item.qty * item.harga;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.className = 'border-b';
            tr.innerHTML = `
                <td class="py-3 pr-2">
                    <p class="font-semibold">${item.nama}</p>
                    <p class="text-xs text-gray-500">${formatCurrency(item.harga)}</p>
                    <input type="hidden" name="items[${id}][produk_id]" value="${item.id}">
                    <input type="hidden" name="items[${id}][harga]" value="${item.harga}">
                </td>
                <td class="py-3 text-center">
                    <input type="number" name="items[${id}][qty]" value="${item.qty}" min="1" class="w-16 border rounded text-center p-1" data-id="${id}">
                </td>
                <td class="py-3 text-right font-medium">${formatCurrency(subtotal)}</td>
                <td class="py-3 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 delete-item-btn" data-id="${id}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            cartItems.appendChild(tr);
        }
        totalText.textContent = formatCurrency(total);
    };

    cartItems.addEventListener('change', (e) => {
        if(e.target.tagName === 'INPUT' && e.target.type === 'number') {
            const id = e.target.dataset.id;
            let newQty = parseInt(e.target.value, 10);
            const batasStok = cart[id].stok_maks;
            
            if (newQty > batasStok) {
                alert(`Stok tidak cukup! Maksimal ${batasStok}`);
                newQty = batasStok; // Kembalikan angka ke batas maksimal
                e.target.value = batasStok; 
            }

            if (newQty > 0) {
                cart[id].qty = newQty;
            } else {
                delete cart[id];
            }
            renderCart();
        }
    });

    cartItems.addEventListener('click', (e) => {
        const deleteButton = e.target.closest('.delete-item-btn');
        if(deleteButton) {
            const id = deleteButton.dataset.id;
            delete cart[id]; 
            renderCart();
        }
    });
});
</script>
@endsection