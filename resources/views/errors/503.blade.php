<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden text-center p-12">
        <div class="mb-8">
            @php
                $settings = app(\App\Settings\GeneralSettings::class);
                $hasImage = $settings->maintenance_image && \Storage::disk('public')->exists($settings->maintenance_image);
            @endphp
            
            @if($hasImage)
                <img src="{{ asset('storage/' . $settings->maintenance_image) }}" 
                     alt="Maintenance" 
                     class="w-full max-h-80 object-contain rounded-lg">
            @else
                <div class="flex justify-center">
                    <div class="bg-blue-50 p-6 rounded-full">
                        <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
            @endif
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Sedang Dalam Pemeliharaan</h1>
        
        <p class="text-base text-gray-600 mb-10 leading-relaxed">
            {{ $message ?? 'Kami sedang melakukan pemeliharaan sistem untuk meningkatkan layanan. Silakan kembali lagi nanti.' }}
        </p>

        <div class="text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
