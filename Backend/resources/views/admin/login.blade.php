<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center">
<div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-sm">
    <div class="text-center mb-6">
        <div class="text-5xl mb-2">🔐</div>
        <h1 class="text-xl font-bold text-gray-800">Admin Login</h1>
        <p class="text-gray-500 text-sm">Rental Mobil Dashboard</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5
                          focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   required autofocus>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5
                          focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   required>
        </div>
        <button type="submit"
                class="w-full bg-blue-700 text-white py-2.5 rounded-lg font-semibold
                       hover:bg-blue-800 transition">
            Login
        </button>
    </form>
</div>
</body>
</html>