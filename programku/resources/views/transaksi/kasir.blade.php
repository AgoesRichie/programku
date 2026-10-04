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
            <div class="row">
                <!-- Looping semua menu dari database -->
                @foreach($menus as $menu)
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <h6 class="card-title">{{ $menu->nama_menu }}</h6>
                            <p class="text-success fw-bold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            <!-- Tombol ini memicu fungsi JavaScript tambahKeKeranjang() -->
                            <button onclick="tambahKeKeranjang({{ $menu->id }})" class="btn btn-primary btn-sm w-100">Tambah</button>
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
    function tambahKeKeranjang(menuId) {
        fetch('/kasir/tambah-keranjang', {
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
                // Jika berhasil ditambah, perbarui tampilan keranjang
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
</script>

</body>
</html>