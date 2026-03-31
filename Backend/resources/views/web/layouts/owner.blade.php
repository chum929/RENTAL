<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Dashboard Owner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar Owner -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col flex-shrink-0">
        <div class="px-6 py-5 border-b border-blue-800">
            <p class="text-xs text-blue-300 uppercase tracking-wider">Owner Panel</p>
            <h1 class="text-lg font-bold mt-0.5">🏢 RentCar Owner</h1>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('owner.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('owner.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('owner.cars') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('owner.cars*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                🚘 Kelola Mobil
            </a>
            <a href="{{ route('owner.bookings') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('owner.bookings*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                📋 Booking Masuk
                @php $pendingCount = \App\Models\Booking::whereHas('car',
                    fn($q) => $q->where('rental_provider_id',
                    auth()->user()->rentalProvider->id ?? 0))
                    ->where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('owner.profile') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ request()->routeIs('owner.profile*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                👤 Profil Rental
            </a>
            <hr class="border-blue-800 my-2">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-800 transition
                      text-blue-300 text-sm">
                🌐 Lihat Website
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-blue-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-700 rounded-full flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-300">Owner</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-2 px-3 py-2 rounded-lg
                               hover:bg-red-800 transition text-sm text-red-300">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-800">@yield('title')</h2>
        </header>
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 text-green-700 rounded-xl">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>