@extends('layouts.app')

@section('title', 'Daftar Menu Jualan')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold">Daftar Menu</h3>
        <a href="{{ route('menu.create') }}" class="btn btn-primary fw-bold">
            + Tambah Menu
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <!-- table-responsive agar tabel bisa digeser ke kanan/kiri di HP -->
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th class="text-center" width="100">Foto</th>
                            <!-- Kolom Kode Menu DIHAPUS dari sini -->
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th class="text-center" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $index => $menu)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if($menu->foto)
                                        <!-- Menampilkan foto dari folder storage -->
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="Foto {{ $menu->nama_menu }}" class="img-thumbnail rounded" style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">Tanpa Foto</span>
                                    @endif
                                </td>
                                <!-- Baris data Kode Menu DIHAPUS dari sini -->
                                <td class="fw-bold">{{ $menu->nama_menu }}</td>
                                <!-- Menampilkan harga dengan format ribuan Rp xx.xxx -->
                                <td class="text-success fw-bold">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-muted small">
                                    {{ $menu->deskripsi ?? '-' }}
                                </td>

                                <!-- TAMBAHKAN KOLOM AKSI INI -->
                                <td class="text-center">
                                    <!-- Form khusus untuk metode DELETE -->
                                    <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->nama_menu }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <!-- colspan diubah dari 6 menjadi 5 karena ada 1 kolom yang dihapus -->
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data menu. Silakan tambah menu baru!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection