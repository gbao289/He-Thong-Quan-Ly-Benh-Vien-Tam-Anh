<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bệnh Viện Đa Khoa Tâm Anh - Đặt Lịch Khám & Chăm Sóc Sức Khỏe')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --tam-primary: #0284c7;
            --tam-primary-hover: #0369a1;
            --tam-accent: #0d9488;
            --tam-dark: #1e293b;
            --tam-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--tam-bg);
            color: var(--tam-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .btn-primary {
            background-color: var(--tam-primary);
            border-color: var(--tam-primary);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--tam-primary-hover);
            border-color: var(--tam-primary-hover);
        }

        .text-primary {
            color: var(--tam-primary) !important;
        }

        .bg-primary {
            background-color: var(--tam-primary) !important;
        }

        .card {
            border-color: #e2e8f0;
            border-radius: 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 0.25rem rgba(2, 132, 199, 0.15);
        }
    </style>

    @stack('styles')
</head>
<body>

    @include('layouts.partials.header')

    <main class="flex-grow-1 py-4">
        <div class="container">
            <x-alert />
            @yield('content')
        </div>
    </main>

    @include('layouts.partials.footer')

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
