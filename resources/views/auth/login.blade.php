<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Multi Role</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body style="background-image: url('{{ asset('images/sekolahHd.jpg') }}'); background-size: cover; background-position: center;">

    <nav class="w-full bg-[#004e92] h-16 flex items-center justify-between px-6 shadow-md fixed top-0 left-0 right-0 z-40">
        <div class="text-white font-semibold text-lg">Al Ma’soem TOEFL CBT System</div>
        <a href="{{ route('landing') }}" class="text-white/80 hover:text-white font-medium transition no-underline">
            Back to Landing Page
        </a>
    </nav>  

    <section class="flex items-center justify-center min-h-screen pt-16">
        <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 w-96 border border-white/40">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Login Sistem</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative mb-4 text-sm">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 👉 INI DIA YANG HILANG KEMARIN! --}}
            <form action="{{ route('login.post') }}" method="POST">
                @csrf 
                {{-- 👉 INI JUGA PENTING BIAR GAK ERROR ROLE --}}
                <input type="hidden" name="role" value="siswa">

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Username / NIS</label>
                    <input type="text" name="username" placeholder="Masukkan NIS (Contoh: 2025001)"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none transition" 
                        value="{{ old('username') }}" required autofocus>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none transition" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95">
                    Login Siswa
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-4">Toefl CBT © 2025</p>
        </div>
    </section>

    <script>lucide.createIcons();</script>

</body>
</html>