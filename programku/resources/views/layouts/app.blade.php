<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shop Mania')</title>
    
    <!-- Mengimpor Bootstrap 5 (Framework CSS Responsif) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .content-area { margin-top: 30px; margin-bottom: 50px; }
    </style>
</head>
<body>

    <!-- Navigasi Utama (Otomatis menyesuaikan layar HP) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Shop Mania</a>
            
            <!-- Tombol Hamburger untuk tampilan HP -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Daftar Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('menu.create') }}">Tambah Menu</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Area Konten Utama (Berubah-ubah sesuai halaman yang dibuka) -->
    <div class="container content-area">
        @yield('content')
    </div>

    <!-- Mengimpor Script Bootstrap (Dibutuhkan agar menu hamburger bisa diklik) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>