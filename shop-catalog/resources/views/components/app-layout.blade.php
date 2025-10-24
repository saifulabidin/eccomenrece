<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $storeName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            @if($storeLogo)
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" height="45" class="me-2 rounded">
                    <span class="fw-bold fs-5">{{ $storeName }}</span>
                </a>
            @else
                <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">{{ $storeName }}</a>
            @endif

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item mx-2">
                        <a class="nav-link fw-semibold px-3 py-2 rounded-pill" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link fw-semibold px-3 py-2 rounded-pill" href="{{ route('catalog') }}">
                            <i class="bi bi-grid me-1"></i>Katalog
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    @livewire('shopping-cart')
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 {{ $storeName }}. All rights reserved.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>