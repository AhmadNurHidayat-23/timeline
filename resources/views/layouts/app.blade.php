<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Susun peristiwa menjadi perjalanan yang mudah dipahami bersama Timeline.">
    @if($readOnly ?? false)<meta name="robots" content="noindex, nofollow">@endif
    <title>@yield('title', 'Cerita dalam Garis Waktu.') — Timeline</title>
    <link rel="icon" href="data:,">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#konten">Lewati ke konten</a>
    <div class="page-shell">
        <header class="site-header">
            @if($readOnly ?? false)
                <span class="wordmark">Timeline<span class="brand-dot" aria-hidden="true"></span></span>
                <span class="eyebrow">TAUTAN PUBLIK · HANYA LIHAT</span>
            @else
                <a class="wordmark" href="{{ route('timelines.index') }}">Timeline<span class="brand-dot" aria-hidden="true"></span></a>
                <nav aria-label="Navigasi utama">
                    <a class="nav-link" href="{{ route('timelines.index') }}">Koleksi saya</a>
                    <a class="button" href="{{ route('timelines.create') }}">Buat Timeline <span aria-hidden="true">↗</span></a>
                </nav>
            @endif
        </header>
        <main id="konten">
            @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
            @yield('content')
        </main>
        <footer class="site-footer">
            <span>Timeline — Setiap peristiwa punya tempat.</span>
            <span>{{ ($readOnly ?? false) ? 'Dibagikan untuk dibaca.' : 'Ruang untuk cerita Anda.' }}</span>
        </footer>
    </div>
</body>
</html>
