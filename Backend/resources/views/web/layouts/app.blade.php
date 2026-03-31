<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rental Mobil') - RentCar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: { extend: { colors: { primary: { DEFAULT:'#2563EB', dark:'#1E40AF' } } } }
      }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-blue-700">
                🚗 RentCar
            </a>
            <!-- Nav Links (desktop) -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}"
                   class="text-gray-600 hover:text-blue-600 transition font-medium
                          {{ request()->routeIs('home') ? 'text-blue-600' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('cars.index') }}"
                   class="text-gray-600 hover:text-blue-600 transition font-medium
                          {{ request()->routeIs('cars.*') ? 'text-blue-600' : '' }}">
                    Daftar Mobil
                </a>
            </div>
            <!-- Auth Buttons / User Menu -->
            <div class="hidden md:flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}"
                       class="text-gray-700 font-medium hover:text-blue-600 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium
                              hover:bg-blue-700 transition">
                        Daftar
                    </a>
                @else
                    @if(auth()->user()->role === 'user')
                        <a href="{{ route('user.dashboard') }}"
                           class="text-gray-600 hover:text-blue-600 transition font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('user.bookings') }}"
                           class="text-gray-600 hover:text-blue-600 transition font-medium">
                            Booking Saya
                        </a>
                        <a href="{{ route('user.notifications') }}" class="relative">
                            <span class="text-gray-600 hover:text-blue-600 text-xl">🔔</span>
                        </a>
                    @elseif(auth()->user()->role === 'owner')
                        <a href="{{ route('owner.dashboard') }}"
                           class="text-gray-600 hover:text-blue-600 transition font-medium">
                            Dashboard Owner
                        </a>
                    @endif
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 bg-blue-50 text-blue-700
                                       px-3 py-2 rounded-lg hover:bg-blue-100 transition">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/'.auth()->user()->photo) }}"
                                     class="w-7 h-7 rounded-full object-cover">
                            @else
                                <span class="w-7 h-7 bg-blue-200 rounded-full flex items-center
                                             justify-center text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="font-medium text-sm">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border py-1 z-50">
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('user.profile') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    👤 Profil Saya
                                </a>
                                <a href="{{ route('user.bookings') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    📋 Booking Saya
                                </a>
                            @elseif(auth()->user()->role === 'owner')
                                <a href="{{ route('owner.profile') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    🏢 Profil Rental
                                </a>
                            @endif
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full text-left px-4 py-2 text-sm text-red-600
                                               hover:bg-red-50">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
            <!-- Mobile menu toggle -->
            <button @click="open = !open" class="md:hidden text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" class="md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 text-gray-700">Beranda</a>
            <a href="{{ route('cars.index') }}" class="block py-2 text-gray-700">Daftar Mobil</a>
            @auth
                @if(auth()->user()->role === 'user')
                    <a href="{{ route('user.dashboard') }}" class="block py-2 text-gray-700">Dashboard</a>
                    <a href="{{ route('user.bookings') }}" class="block py-2 text-gray-700">Booking Saya</a>
                @elseif(auth()->user()->role === 'owner')
                    <a href="{{ route('owner.dashboard') }}" class="block py-2 text-gray-700">Dashboard Owner</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left py-2 text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-gray-700">Login</a>
                <a href="{{ route('register') }}" class="block py-2 text-blue-600 font-semibold">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
    <div class="max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-7xl mx-auto mt-4 px-4">
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            ❌ {{ session('error') }}
        </div>
    </div>
@endif

<!-- KONTEN UTAMA -->
<main class="flex-1">
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="bg-gray-800 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-3">🚗 RentCar</h3>
                <p class="text-gray-400 text-sm">
                    Platform sewa mobil terpercaya. Temukan mobil terbaik di kotamu.
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Navigasi</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Beranda</a></li>
                    <li><a href="{{ route('cars.index') }}" class="hover:text-white">Daftar Mobil</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white">Daftar sebagai Owner</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3">Kontak</h4>
                <p class="text-sm text-gray-400">📧 info@rentcar.id</p>
                <p class="text-sm text-gray-400">📱 0800-1234-5678</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
            © {{ date('Y') }} RentCar. Dibuat untuk tugas akhir semester 4.
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>