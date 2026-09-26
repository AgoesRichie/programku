<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function create()
    {
        // Logika untuk menebak kode menu selanjutnya
        $menuTerakhir = Menu::orderBy('id', 'desc')->first();

        if (!$menuTerakhir) {
            $kodeBaru = 'MN-001';
        } else {
            $angkaTerakhir = (int) substr($menuTerakhir->kode_menu, 3);
            $kodeBaru = 'MN-' . str_pad($angkaTerakhir + 1, 3, '0', STR_PAD_LEFT);
        }

        // Kirim variabel $kodeBaru ke file view
        return view('menu.create', compact('kodeBaru'));
    }

    public function store(Request $request)
    {
        // 1. WAJIB DI ATAS: Hapus titik dari harga dulu!
        // Mengubah "25.000" menjadi "25000"
        if ($request->has('harga')) {
            $request->merge([
                'harga' => str_replace('.', '', $request->harga)
            ]);
        }
    
        // 1. Validasi input (kode_menu dihapus dari validasi karena dibuat otomatis)
        $request->validate([
            'nama_menu' => 'required',
            'deskripsi' => 'required|string|max:1000',
            'harga'     => 'required|numeric',
            'foto'      => 'required|image|mimes:jpeg,png,jpg,gif|max:4096', // Maksimal 4MB
        ]);

        // 2. Logika Auto-Generate Kode Menu (Prefix: MN-)
        $menuTerakhir = Menu::orderBy('id', 'desc')->first();

        if (!$menuTerakhir) {
            // Jika belum ada data sama sekali, mulai dari MN-001
            $kodeBaru = 'MN-001';
        } else {
            // Ambil angka dari kode terakhir (contoh: dari 'MN-005' ambil angka '5')
            $angkaTerakhir = (int) substr($menuTerakhir->kode_menu, 3);
            $angkaBaru = $angkaTerakhir + 1;
            
            // Format kembali menjadi MN- ditambah 3 digit angka (contoh: MN-006)
            $kodeBaru = 'MN-' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
        }

        // 3. LOGIKA KOMPRESI GAMBAR BAWAAN PHP (TANPA LIBRARY)
        $lokasiFoto = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . uniqid() . '.jpg';
            
            // Buat folder jika belum ada di storage/app/public/foto_menu
            $folderTujuan = storage_path('app/public/foto_menu');
            if (!file_exists($folderTujuan)) {
                mkdir($folderTujuan, 0755, true);
            }
            
            $pathTujuan = $folderTujuan . '/' . $namaFile;

            // Ambil informasi ukuran gambar
            $infoGambar = getimagesize($file->getPathname());
            $lebarAsli = $infoGambar[0];
            $tinggiAsli = $infoGambar[1];
            $tipeGambar = $infoGambar[2]; // 2 = JPG, 3 = PNG

            // Tentukan ukuran baru (maksimal lebar 800px)
            $lebarBaru = 800;
            if ($lebarAsli > $lebarBaru) {
                $tinggiBaru = ($tinggiAsli / $lebarAsli) * $lebarBaru;
            } else {
                $lebarBaru = $lebarAsli;
                $tinggiBaru = $tinggiAsli;
            }

            // Buat kanvas kosong dengan ukuran baru
            $gambarBaru = imagecreatetruecolor($lebarBaru, $tinggiBaru);

            // Baca file gambar sumber
            if ($tipeGambar == IMAGETYPE_PNG) {
                // Beri latar putih untuk PNG transparan agar tidak jadi hitam saat diubah ke JPG
                $bgPutih = imagecolorallocate($gambarBaru, 255, 255, 255);
                imagefill($gambarBaru, 0, 0, $bgPutih);
                $sumberGambar = imagecreatefrompng($file->getPathname());
            } else {
                $sumberGambar = imagecreatefromjpeg($file->getPathname());
            }

            // Proses kompresi dan resize
            imagecopyresampled($gambarBaru, $sumberGambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebarAsli, $tinggiAsli);
            
            // Simpan hasilnya sebagai JPG dengan kualitas 60%
            imagejpeg($gambarBaru, $pathTujuan, 60);

            // Bersihkan memori server
            imagedestroy($sumberGambar);
            imagedestroy($gambarBaru);

            // Catat nama file untuk disimpan di database
            $lokasiFoto = 'foto_menu/' . $namaFile;
        }

        // 4. Simpan data ke database beserta kode otomatisnya
        Menu::create([
            'kode_menu' => $kodeBaru,
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'foto'      => $lokasiFoto,
        ]);

        // 4. Kembalikan ke form dengan pesan sukses
        // KODE BARU DITAMPILKAN DI PESAN SUKSES
        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan dengan nama: ' . $request->nama_menu);
    }

    public function index()
    {
        // Ambil semua data menu dari yang terbaru (descending)
        $menus = Menu::orderBy('nama_menu', 'asc')->get();
        
        return view('menu.index', compact('menus'));
    }

    public function destroy($id)
    {
        // 1. Cari data menu berdasarkan ID
        $menu = Menu::findOrFail($id);

        // 2. Cek apakah menu ini memiliki foto
        if ($menu->foto) {
            // Hapus file foto dari dalam folder storage
            $pathFoto = storage_path('app/public/' . $menu->foto);
            if (file_exists($pathFoto)) {
                unlink($pathFoto); // Perintah native PHP untuk menghapus file
            }
        }

        // 3. Hapus data dari database SQLite
        $menu->delete();

        // 4. Kembali ke halaman daftar dengan pesan sukses
        return redirect()->route('menu.index')->with('success', 'Data menu dan foto berhasil dihapus!');
    }
}