<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spekta Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96 border-t-8 border-[#990000]">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#990000]">SPEKTA</h1>
            <p class="text-gray-500 italic text-xs tracking-widest uppercase">Academy Portal</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-xs font-bold text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Gmail / Email</label>
                <input type="email" name="email" class="w-full border p-2.5 rounded-lg focus:outline-none focus:border-[#990000]" placeholder="admin@gmail.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full border p-2.5 rounded-lg focus:outline-none focus:border-[#990000]" placeholder="********" required>
            </div>
            <button type="submit" class="w-full bg-[#990000] text-white py-3 rounded-xl font-bold hover:bg-red-800 transition duration-300 shadow-lg">
                MASUK KE DASHBOARD
            </button>
        </form>
    </div>
</body>
</html>
