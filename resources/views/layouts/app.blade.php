<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Dapur Mamina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-orange': '#E37424',
                        'brand-orange-dark': '#C55A11',
                        'brand-sidebar': '#3A3A3A',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Sembunyikan scrollbar untuk Chrome, Safari dan Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Sembunyikan scrollbar untuk IE, Edge dan Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE dan Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-gray-100 overflow-hidden"> <!-- Tambah overflow-hidden biar nggak scroll double -->

    <div class="flex h-screen relative">

        {{-- [BARU] Overlay Gelap untuk Mobile saat Sidebar Terbuka --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300 md:hidden"></div>

        {{-- Sidebar --}}
        {{-- [UBAHAN] Sidebar jadi fixed di HP (-translate-x-full), tapi relative & muncul di Laptop (md:relative md:translate-x-0) --}}
        <aside id="sidebar" class="w-64 flex flex-col bg-[#4A3728] text-white fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-50">
            <div class="h-20 flex items-center justify-center border-b border-white/10 relative">
                <img src="{{ asset('image/mamina.jpg') }}" alt="Logo Dapur Mamina" class="h-16 w-16 rounded-full">
                {{-- [BARU] Tombol Silang (Close) khusus di HP --}}
                <button id="close-sidebar" class="absolute right-4 text-gray-300 hover:text-white md:hidden">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto no-scrollbar">
                <p class="px-4 text-xs text-gray-400 uppercase tracking-wider">Main</p>
                {{-- Link Dashboard --}}
                @if(auth()->user()->role === 'admin')
                    <a href="{{ url('/') }}" class="flex items-center px-4 py-2.5 rounded-lg font-semibold
                        {{ request()->is('/') ? 'bg-brand-orange-dark/50' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-chart-pie w-6 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('kelola-kasir.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition duration-200 {{ request()->routeIs('kelola-kasir.*') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-users w-6 text-lg mr-3"></i> Kelola Kasir
                    </a>
                    <p class="px-4 pt-4 text-xs text-gray-400 uppercase tracking-wider">Laporan</p>
                    <a href="{{ route('laporan') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->is('laporan*') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-file-alt w-6 text-center"></i>
                        <span>Laporan</span>
                    </a>
                    <p class="px-4 pt-4 text-xs text-gray-400 uppercase tracking-wider">Master Data</p>
                    <a href="{{ route('produk.index') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->is('produk*') || request()->is('tambah-produk') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-tags w-6 text-center"></i>
                        <span>Produk</span>
                    </a>
                    <a href="{{ route('satuan.index') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->routeIs('satuan.*') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-box-open w-6 text-center"></i>
                        <span>Satuan</span>
                    </a>
                    <p class="px-4 pt-4 text-xs text-gray-400 uppercase tracking-wider">Inventori</p>
                    <a href="{{ url('/daftar-stok') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->is('daftar-stok') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-boxes-stacked w-6 text-center"></i>
                        <span>Daftar Stok</span>
                    </a>
                    <a href="{{ route('stok.masuk') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->routeIs('stok.masuk') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-dolly w-6 text-center"></i>
                        <span>Tambah Stok</span>
                    </a>
                    <a href="{{ url('/opname-stok') }}" class="flex items-center px-4 py-2.5 rounded-lg
                        {{ request()->is('opname-stok') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                        <i class="fa-solid fa-tasks w-6 text-center"></i>
                        <span>Opname Stok</span>
                    </a>
                @endif

                {{-- Link Kasir --}}
                <a href="{{ url('/kasir') }}" class="flex items-center px-4 py-2.5 rounded-lg
                    {{ request()->is('kasir') ? 'bg-brand-orange-dark/50 font-semibold' : 'hover:bg-white/10' }}">
                    <i class="fa-solid fa-cash-register w-6 text-center"></i>
                    <span>Kasir</span>
                </a>
            </nav>

            {{-- Bagian User Menu & Logout --}}
            <div class="h-20 flex items-center justify-center border-t border-white/10 px-4 relative">
                <div class="relative w-full">
                    <button id="user-menu-button" class="w-full flex items-center justify-between text-white font-semibold focus:outline-none hover:bg-white/10 p-2 rounded-lg transition-colors duration-200">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-circle text-xl"></i>
                            <span class="truncate">{{ Auth::user()->name }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-up transition-transform duration-300" id="user-chevron"></i>
                    </button>
                    <div id="user-menu" class="hidden absolute bottom-full mb-2 w-full bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                        <a href="{{ route('profile.edit') }}" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-user-edit w-6 mr-2"></i>Edit Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                               <i class="fa-solid fa-right-from-bracket w-6 mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col w-full h-screen overflow-hidden">
            {{-- [UBAHAN] Header disesuaikan. Tombol Hamburger di kiri, Judul di kanan --}}
            <header class="h-20 bg-brand-orange flex items-center justify-between md:justify-end px-4 md:px-8 shrink-0">
                 {{-- Tombol Hamburger Khusus HP --}}
                 <button id="hamburger-btn" class="text-white hover:text-gray-200 focus:outline-none md:hidden p-2">
                     <i class="fa-solid fa-bars text-2xl"></i>
                 </button>

                 <div class="bg-brand-orange-dark px-4 md:px-6 py-2 rounded-lg shadow-md">
                     <h1 class="text-lg md:text-xl font-bold text-white">@yield('header_title', 'Dashboard')</h1>
                 </div>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika User Menu (Profile)
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            const userChevron = document.getElementById('user-chevron');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                    userChevron.classList.toggle('rotate-180');
                });

                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                        userChevron.classList.remove('rotate-180');
                    }
                });
            }

            // [BARU] Logika Sidebar Mobile (Hamburger Menu)
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const closeSidebarBtn = document.getElementById('close-sidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            if(hamburgerBtn && sidebar && overlay && closeSidebarBtn) {
                hamburgerBtn.addEventListener('click', toggleSidebar);
                closeSidebarBtn.addEventListener('click', toggleSidebar);
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>
