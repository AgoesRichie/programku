@extends('layouts.app')

@section('title', 'Tambah Data Menu Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 text-primary">Tambah Menu Baru</h3>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('menu.store') }}" method="POST">
                    @csrf 

                    <!-- 1. Kode Menu (Auto-Generate, Readonly) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Kode Menu</label>
                        <input type="text" name="kode_menu" class="form-control form-control-lg bg-light" value="{{ $kodeBaru }}" readonly>
                        <small class="text-muted">Kode ini dibuat otomatis oleh sistem dan tidak dapat diubah.</small>
                    </div>

                    <!-- 2. Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control form-control-lg @error('nama_menu') is-invalid @enderror" value="{{ old('nama_menu') }}" placeholder="Contoh: Ayam Goreng" required>
                        @error('nama_menu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- 3. Harga (Dengan Script Pemisah Ribuan) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Harga (Rp)</label>
                        <input type="text" inputmode="numeric" id="harga" name="harga" class="form-control form-control-lg @error('harga') is-invalid @enderror" value="{{ old('harga') }}" placeholder="Contoh: 50.000" required>
                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- 4. Upload Foto Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Foto Menu (Opsional)</label>
                        <input type="file" name="foto" class="form-control form-control-lg @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Gambar akan otomatis dikompres agar website tetap ringan.</small>
                    </div>

                    <!-- 5. Deskripsi -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Masukkan detail produk...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Simpan Menu</button>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const inputHarga = document.getElementById('harga');

    // Saat kolom kehilangan fokus (lost focus / blur)
    inputHarga.addEventListener('blur', function() {
        // Hilangkan semua karakter selain angka
        let value = this.value.replace(/[^0-9]/g, '');
        if (value !== '') {
            // Format ke ribuan standar Indonesia (menggunakan titik)
            this.value = parseInt(value, 10).toLocaleString('id-ID');
        }
    });

    // Saat kolom diklik lagi untuk diedit (focus)
    inputHarga.addEventListener('focus', function() {
        // Hilangkan titik agar mudah diedit ulang
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>

@endsection