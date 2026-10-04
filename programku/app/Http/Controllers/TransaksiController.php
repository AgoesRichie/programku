<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // Jangan lupa panggil model Menu

class TransaksiController extends Controller
{
    public function index()
    {
        // 1. Mengambil semua data menu
        $menus = Menu::all(); 
        
        // 2. Mengambil daftar jenis menu yang unik
        // (Pastikan nama kolom 'jenis_menu' sesuai dengan yang ada di migration/database Anda)
        $kategori_menu = Menu::select('jenis_menu')->distinct()->pluck('jenis_menu');
        
        // 3. Wajib menyertakan 'kategori_menu' di dalam compact agar datanya terkirim ke layar
        return view('transaksi.kasir', compact('menus', 'kategori_menu'));
    }

    // Fungsi 1: Menambah menu ke keranjang dengan AMAN
    public function tambahKeranjang(Request $request)
    {
        // Cari menu berdasarkan ID
        $menu = Menu::find($request->menu_id);
        // Tangkap jumlah porsi yang dikirim dari layar
        $qty_tambahan = $request->qty; 

        if (!$menu) {
            return response()->json(['status' => 'error', 'pesan' => 'Menu tidak ditemukan!'], 404);
        }

        $keranjang = session()->get('keranjang', []);

        // Jika menu sudah ada di keranjang, tambahkan jumlahnya
        if (isset($keranjang[$menu->id])) {
            $keranjang[$menu->id]['jumlah'] += $qty_tambahan;
            $keranjang[$menu->id]['subtotal'] = $keranjang[$menu->id]['jumlah'] * $menu->harga;
        } 
        // Jika belum ada, masukkan sebagai barang baru
        else {
            if ($qty_tambahan > 0) {
                $keranjang[$menu->id] = [
                    'nama'     => $menu->nama_menu,
                    'harga'    => $menu->harga,
                    'jumlah'   => $qty_tambahan,
                    'subtotal' => $menu->harga * $qty_tambahan
                ];
            }
        }

        session()->put('keranjang', $keranjang);

        return response()->json(['status' => 'sukses']);
    }

    // Fungsi 2: Mengirim data keranjang ke layar kasir
    public function dataKeranjang()
    {
        $keranjang = session()->get('keranjang', []);
        $total_bayar = 0;

        foreach ($keranjang as $item) {
            $total_bayar += $item['subtotal'];
        }

        return response()->json([
            'keranjang' => $keranjang,
            'total_bayar' => $total_bayar
        ]);
    }

    public function hapusKeranjang(Request $request)
    {
        // Ambil data keranjang dari memori (session)
        $keranjang = session()->get('keranjang', []);

        // Cek apakah menu yang mau dihapus ada di keranjang
        if (isset($keranjang[$request->menu_id])) {
            // Hapus menu tersebut dari array keranjang
            unset($keranjang[$request->menu_id]);
            
            // Simpan kembali data keranjang yang sudah diperbarui ke memori
            session()->put('keranjang', $keranjang);
        }

        return response()->json(['status' => 'sukses', 'pesan' => 'Menu berhasil dihapus']);
    }
}
