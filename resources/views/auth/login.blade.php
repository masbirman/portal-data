<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Data AN-TKA Disdik Sulteng</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-6xl">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left Side - Login Form -->
                <div class="p-8 lg:p-12">
                    <!-- Logo & Title -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <img src="{{ asset('storage/logo-sulteng.png') }}" alt="Logo Sulawesi Tengah" class="h-12 w-auto">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Portal Data AN-TKA</h1>
                                <p class="text-sm text-gray-600">Disdik Prov. Sulteng</p>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Selamat Datang</h2>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.authenticate') }}" class="space-y-6">
                        @csrf

                        <!-- Success Messages -->
                        @if (session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        @foreach ($errors->all() as $error)
                                            <p class="text-sm">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Username/Email Input -->
                        <div>
                            <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                                Username atau Email
                            </label>
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                value="{{ old('login') }}"
                                required 
                                autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Masukkan username atau email"
                            >
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Masukkan password"
                            >
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Cloudflare Turnstile -->
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl"
                        >
                            Login
                        </button>
                    </form>

                    <!-- Back to Home -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('public.landing') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            ← Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <!-- Right Side - Illustration -->
                <div class="hidden lg:block bg-gradient-to-br from-blue-600 to-blue-800 p-12 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-500 opacity-20 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-blue-700 opacity-20 blur-3xl"></div>

                    <div class="relative z-10 h-full flex flex-col justify-center">
                        <img src="{{ asset('images/login-bg.svg') }}" alt="Login Illustration" class="w-full max-w-md mx-auto mb-8 drop-shadow-2xl">
                        
                        <div class="text-white space-y-4">
                            <h3 class="text-2xl font-bold">Portal Terpadu</h3>
                            <p class="text-blue-100">
                                Sistem informasi terintegrasi untuk pengelolaan data Asesmen Nasional dan Tes Kemampuan Akademik di Sulawesi Tengah.
                            </p>
                            
                            <div class="space-y-3 pt-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-blue-100">Akses panel sesuai role Anda</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-blue-100">Data real-time dan akurat</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-blue-100">Keamanan data terjamin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white text-sm">
            <p>© {{ date('Y') }} Portal Data AN-TKA Disdik Sulteng</p>
        </div>
    </div>
</body>

</html>
