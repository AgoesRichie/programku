@extends('layouts.app')

@section('title', 'Edit Menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="mb-3 text-start">
            <a href="{{ route('menu.index') }}" class="btn btn-sm btn-outline-secondary fw-bold">
                &laquo; Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 text-primary fw-bold">Edit Data Menu</h3>

                <!-- PENTING: Untuk edit, method di HTML tetap POST, tapi kita tambah @method('PUT') di bawahnya -->
                <form action="{{ route('menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf 
                    @method('PUT')

                    <!-- Kode Menu (Readonly, tidak boleh diedit) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Kode Menu</label>
                        <input type="text" class="form-control form-control-lg bg-light" value="{{ $menu->kode_menu }}" readonly>
                    </div>

                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control form-control-lg @error('nama_menu') is-invalid @enderror" value="{{ old('nama_menu', $menu->nama_menu) }}" required>
                        @error('nama_menu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Jenis Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jenis Menu</label>
                        <select name="jenis_menu" class="form-select form-select-lg @error('jenis_menu') is-invalid @enderror" required>
                            <option value="" disabled>-- Pilih Jenis Menu --</option>
                            <option value="Makanan" {{ old('jenis_menu', $menu->jenis_menu) == 'Makanan' ? 'selected' : '' }}>🍽️ Makanan</option>
                            <option value="Minuman" {{ old('jenis_menu', $menu->jenis_menu) == 'Minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                            <option value="Camilan" {{ old('jenis_menu', $menu->jenis_menu) == 'Camilan' ? 'selected' : '' }}>🍿 Camilan</option>
                        </select>
                        @error('jenis_menu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Harga (Rp)</label>
                        <input type="text" inputmode="numeric" id="harga" name="harga" class="form-control form-control-lg @error('harga') is-invalid @enderror" value="{{ old('harga', number_format($menu->harga, 0, ',', '.')) }}" required>
                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Foto -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Foto Menu Baru (Opsional)</label>
                        
                        <!-- Tampilkan foto lama jika ada -->
                        @if($menu->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $menu->foto) }}" class="img-thumbnail rounded" style="height: 100px;">
                            </div>
                            <small class="text-muted d-block mb-2">Biarkan kosong jika tidak ingin mengganti foto.</small>
                        @endif

                        <input type="file" name="foto" class="form-control form-control-lg @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script JavaScript Harga -->
<script>
    const inputHarga = document.getElementById('harga');
    inputHarga.addEventListener('blur', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value !== '') {
            this.value = parseInt(value, 10).toLocaleString('id-ID');
        }
    });
    inputHarga.addEventListener('focus', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection