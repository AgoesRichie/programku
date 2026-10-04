<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir POS - Richie Shop</title>
    <!-- Wajib: Token CSRF agar Laravel mengizinkan pengiriman data dari JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Desain Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <div class="row">
        
        <!-- KOLOM KIRI: Daftar Menu -->
        <div class="col-md-8">
            <h4 class="mb-3">Daftar Menu</h4>

            <!-- Tombol Filter Kategori -->
            <div class="mb-3" id="area-filter">
                <button class="btn btn-dark btn-sm filter-btn" onclick="filterMenu('semua', this)">Semua</button>
                @foreach($kategori_menu as $kategori)
                    <button class="btn btn-outline-dark btn-sm filter-btn" onclick="filterMenu('{{ $kategori }}', this)">
                        {{ ucfirst($kategori) }}
                    </button>
                @endforeach
            </div>

            <div class="row" id="daftar-menu-container">
            
                <!-- Looping semua menu dari database -->
                @foreach($menus as $menu)
                <!-- Tambahkan class 'menu-item' dan atribut 'data-kategori' -->
                <div class="col-md-3 mb-3 menu-item" data-kategori="{{ $menu->jenis_menu }}">
                    <div class="card shadow-sm h-100">
                        <!-- BAGIAN GAMBAR DITAMBAHKAN DI SINI -->
                        <!-- Asumsi: nama kolom gambar di database Anda adalah 'gambar' -->
                        <!-- Gunakan style object-fit agar gambar tidak gepeng dan ukurannya seragam -->
                        <img src="{{ asset('storage/' . $menu->foto) }}" class="card-img-top" alt="{{ $menu->nama_menu }}" style="height: 120px; object-fit: cover;">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <h6 class="card-title">{{ $menu->nama_menu }}</h6>
                            <p class="text-success fw-bold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            
                            <!-- Pengaturan Qty Sementara di Layar -->
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <button onclick="kurangiQtyLokal({{ $menu->id }})" class="btn btn-outline-secondary btn-sm px-2 fw-bold">-</button>
                                <span class="mx-3 fw-bold" id="input-qty-{{ $menu->id }}">1</span>
                                <button onclick="tambahQtyLokal({{ $menu->id }})" class="btn btn-outline-secondary btn-sm px-2 fw-bold">+</button>
                            </div>

                            <!-- Tombol Kirim ke Keranjang -->
                            <button onclick="prosesTambahKeKeranjang({{ $menu->id }})" class="btn btn-primary btn-sm w-100">Tambah</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- KOLOM KANAN: Keranjang Pesanan -->
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Keranjang Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Subtotal</th>
                                <th></th> <!-- Kolom baru untuk tombol hapus -->
                            </tr>
                        </thead>
                        <!-- JavaScript akan memunculkan daftar pesanan di dalam tbody ini -->
                        <tbody id="area-keranjang">
                            <tr><td colspan="3" class="text-center text-muted">Belum ada pesanan</td></tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5 class="text-danger fw-bold">Rp <span id="total-bayar">0</span></h5>
                    </div>
                    <button class="btn btn-success w-100 btn-lg">Proses Pembayaran</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- LOGIKA JAVASCRIPT UNTUK AJAX (TANPA RELOAD) -->
<script>
    // Ambil token keamanan dari meta tag di atas
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. Fungsi mengirim ID Menu ke server (berjalan saat tombol Tambah diklik)
    // Fungsi mengubah angka kuantitas naik (+1)
    function tambahQtyLokal(id) {
        let qtyElement = document.getElementById('input-qty-' + id);
        let qty = parseInt(qtyElement.innerText);
        qtyElement.innerText = qty + 1;
    }

    // Fungsi mengubah angka kuantitas turun (-1), minimal angka adalah 1
    function kurangiQtyLokal(id) {
        let qtyElement = document.getElementById('input-qty-' + id);
        let qty = parseInt(qtyElement.innerText);
        if(qty > 1) { 
            qtyElement.innerText = qty - 1;
        }
    }

    // Fungsi mengirim pesanan ke server saat tombol biru diklik
    function prosesTambahKeKeranjang(menuId) {
        // Ambil angka kuantitas dari layar
        let qtyElement = document.getElementById('input-qty-' + menuId);
        let qtyInput = parseInt(qtyElement.innerText);

        fetch('/kasir/tambah-keranjang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                menu_id: menuId,
                qty: qtyInput // Kirim jumlah yang disetel kasir
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'sukses') {
                // Kembalikan angka di menu jadi 1
                qtyElement.innerText = '1';
                
                // Panggil fungsi muatKeranjang() untuk perbarui tabel sebelah kanan
                muatKeranjang(); 
            }
        });
    }

    // 2. Fungsi mengambil data keranjang dari server dan menampilkannya di layar
    function muatKeranjang() {
        fetch('/kasir/data-keranjang')
        .then(response => response.json())
        .then(data => {
            let htmlKeranjang = '';
            
            // Jika keranjang tidak kosong, buat baris tabel
            if(Object.keys(data.keranjang).length > 0) {
                for (const [id, item] of Object.entries(data.keranjang)) {
                    htmlKeranjang += `
                        <tr>
                            <td>${item.nama}</td>
                            <td class="text-center">${item.jumlah}</td>
                            <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                            <td class="text-center">
                                <button onclick="hapusDariKeranjang(${id})" class="btn btn-danger btn-sm text-white fw-bold">X</button>
                            </td>
                        </tr>
                    `;
                }
            } else {
                htmlKeranjang = `<tr><td colspan="3" class="text-center text-muted">Belum ada pesanan</td></tr>`;
            }
            
            // Suntikkan kode HTML yang sudah dibuat ke dalam <tbody id="area-keranjang">
            document.getElementById('area-keranjang').innerHTML = htmlKeranjang;
            
            // Ubah angka total bayar
            document.getElementById('total-bayar').innerText = data.total_bayar.toLocaleString('id-ID');
        });
    }

    // Fungsi menghapus menu dari keranjang
    function hapusDariKeranjang(menuId) {
        fetch('/kasir/hapus-keranjang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ menu_id: menuId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'sukses') {
                // Refresh tampilan keranjang setelah berhasil dihapus
                muatKeranjang(); 
            }
        });
    }

    // 3. Otomatis muat data keranjang saat halaman pertama kali dibuka
    document.addEventListener('DOMContentLoaded', function() {
        muatKeranjang();
    });

    // Fungsi untuk menyaring menu berdasarkan tombol kategori yang diklik
    function filterMenu(kategori, tombolDiklik) {
        // 1. Ubah warna semua tombol menjadi outline (tidak aktif)
        let semuaTombol = document.querySelectorAll('.filter-btn');
        semuaTombol.forEach(btn => {
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-outline-dark');
        });

        // 2. Ubah warna tombol yang sedang diklik menjadi gelap (aktif)
        tombolDiklik.classList.remove('btn-outline-dark');
        tombolDiklik.classList.add('btn-dark');

        // 3. Tampilkan atau sembunyikan kartu menu sesuai kategori
        let semuaMenu = document.querySelectorAll('.menu-item');
        semuaMenu.forEach(item => {
            let kategoriMenu = item.getAttribute('data-kategori');
            
            // Jika pilih "semua" atau kategorinya cocok, tampilkan. Jika tidak, sembunyikan.
            if (kategori === 'semua' || kategori === kategoriMenu) {
                item.style.display = 'block'; // Tampilkan
            } else {
                item.style.display = 'none';  // Sembunyikan
            }
        });
    }

</script>

</body>
</html>