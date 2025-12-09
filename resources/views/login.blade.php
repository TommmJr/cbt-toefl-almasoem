@extends('layouts.guest')

@section('title', 'Login - Multi Role')

@section('content')
<div class="min-h-screen" 
     style="background-image: url('{{ asset('images/sekolahHd.jpg') }}'); background-size: cover; background-position: center;">
    
    {{-- Navbar --}}
    <nav class="w-full bg-[#004e92] h-16 flex items-center justify-between px-6 shadow-md fixed top-0 left-0 right-0 z-40">
        <div class="text-white font-semibold text-lg">Al Ma'soem TOEFL CBT System</div>
        <a href="{{ route('landing') }}" 
           class="text-white/80 hover:text-white font-medium transition no-underline">
            Back to Landing Page
        </a>
    </nav>

    {{-- Login Box --}}
    <section class="flex items-center justify-center min-h-screen pt-16">
        <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl p-8 w-96 border border-white/40">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Login Sistem</h2>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-none space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start">
                                <i data-lucide="alert-circle" size="16" class="mr-2 mt-0.5 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-start">
                    <i data-lucide="check-circle" size="16" class="mr-2 mt-0.5 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Role Selection --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Pilih Role</label>
                    <select name="role" 
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                            required>
                        <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                {{-- Username --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Username</label>
                    <input type="text" 
                           name="username" 
                           placeholder="Masukkan username"
                           value="{{ old('username') }}"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                           required>
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" 
                           name="password" 
                           placeholder="Masukkan password"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" 
                           required>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-xl transition">
                    Login
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-4">TOEFL CBT © 2025</p>
        </div>
    </section>
</div>
@endsection