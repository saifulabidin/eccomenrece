<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $storeName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            @if($storeLogo)
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" height="40" class="d-inline-block align-top">
                </a>
            @else
                <a class="navbar-brand" href="{{ route('home') }}">{{ $storeName }}</a>
            @endif
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalog') }}">Katalog</a>
                    </li>
                    <li class="nav-item">
                        @livewire('shopping-cart')
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 {{ $storeName }}. All rights reserved.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>