<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota')->unique(); // Contoh: TRX-001
            $table->integer('total_harga');
            $table->integer('uang_bayar');
            $table->integer('uang_kembali');
            $table->timestamps(); // Otomatis mencatat tanggal & waktu transaksi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
