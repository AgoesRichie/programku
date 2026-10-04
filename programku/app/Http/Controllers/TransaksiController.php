<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // Jangan lupa panggil model Menu

class TransaksiController extends Controller
{
    public function index()
    {
        // Mengambil semua data menu agar kasir bisa memilih pesanan
        $menus = Menu::all(); 
        
        return view('transaksi.kasir', compact('menus'));
    }

    // Fungsi 1: Menambah menu ke keranjang dengan AMAN
    public function tambahKeranjang(Request $request)
    {
        // KEAMANAN TERBAIK: Kita cari menu di database berdasarkan ID yang dikirim
        $menu = Menu::find($request->menu_id);

        if (!$menu) {
            return response()->json(['status' => 'error', 'pesan' => 'Menu tidak ditemukan!'], 404);
        }

        // Ambil data keranjang saat ini dari memori (session)
        $keranjang = session()->get('keranjang', []);

        // Jika menu sudah ada di keranjang, tambah jumlahnya (qty)
        if (isset($keranjang[$menu->id])) {
            $keranjang[$menu->id]['jumlah']++;
            $keranjang[$menu->id]['subtotal'] = $keranjang[$menu->id]['jumlah'] * $menu->harga;
        } 
        // Jika belum ada, masukkan sebagai barang baru
        else {
            $keranjang[$menu->id] = [
                'nama'     => $menu->nama_menu,
                'harga'    => $menu->harga, // HARGA ASLI DARI DATABASE
                'jumlah'   => 1,
                'subtotal' => $menu->harga
            ];
        }

        // Simpan kembali ke memori
        session()->put('keranjang', $keranjang);

        return response()->json(['status' => 'sukses', 'pesan' => 'Berhasil ditambahkan']);
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
