<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-60 bg-blue-900 text-white flex flex-col flex-shrink-0">
        <div class="px-5 py-4 border-b border-blue-800">
            <h1 class="font-bold text-lg">🚗 Rental Admin</h1>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @foreach([
                ['/admin/dashboard', '📊', 'Dashboard',          'admin.dashboard'],
                ['/admin/providers', '🏢', 'Penyedia Rental',    'admin.providers'],
                ['/admin/users',     '👥', 'Kelola User',        'admin.users'],
                ['/admin/cars',      '🚘', 'Monitor Mobil',      'admin.cars'],
                ['/admin/bookings',  '📋', 'Monitor Booking',    'admin.bookings'],
                ['/admin/cities',    '📍', 'Kelola Kota',        'admin.cities'],
                ['/admin/reviews',   '⭐', 'Moderasi Review',    'admin.reviews'],
            ] as [$url, $icon, $label, $route])
            <a href="{{ $url }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                      {{ request()->routeIs($route) ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-800' }}">
                <span>{{ $icon }}</span> {{ $label }}
            </a>
            @endforeach
        </nav>
        <div class="px-3 py-4 border-t border-blue-800">
            <p class="text-xs text-blue-300 mb-2 px-3">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="w-full flex items-center gap-2 px-3 py-2 rounded-lg
                               text-sm hover:bg-red-700 transition text-red-300">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Konten -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b px-6 py-3 flex-shrink-0">
            <h2 class="text-base font-semibold text-gray-800">@yield('title')</h2>
        </header>
        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-300 text-green-700
                            px-4 py-3 rounded-lg text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-300 text-red-700
                            px-4 py-3 rounded-lg text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>