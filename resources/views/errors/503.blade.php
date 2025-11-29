<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div
        class="max-w-2xl w-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl shadow-xl overflow-hidden text-center p-6 md:p-8">
        <div class="mb-6">
            @php
                $settings = app(\App\Settings\GeneralSettings::class);
                $hasImage =
                    $settings->maintenance_image && \Storage::disk('public')->exists($settings->maintenance_image);
            @endphp

            @if ($hasImage)
                <img src="{{ asset('storage/' . $settings->maintenance_image) }}" alt="Maintenance"
                    class="w-full max-h-80 object-contain rounded-lg">
            @else
                <div class="flex justify-center">
                    <div class="bg-blue-50 p-6 rounded-full">
                        <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center gap-3 mb-3">
            <div class="relative">
                <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Sedang Dalam Pemeliharaan</h1>
        </div>

        <p class="text-base text-gray-300 mb-4 leading-relaxed">
            {{ $settings->maintenance_message ?? 'Kami sedang melakukan pemeliharaan sistem untuk meningkatkan layanan. Silakan kembali lagi nanti.' }}
        </p>

        @if ($settings->maintenance_estimated_time)
            <div
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Estimasi selesai: {{ $settings->maintenance_estimated_time }}</span>
            </div>
        @else
            <div class="mb-6"></div>
        @endif

        <div class="text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>

</html>
