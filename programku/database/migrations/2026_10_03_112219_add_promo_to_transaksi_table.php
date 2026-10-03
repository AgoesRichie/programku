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
        Schema::table('transaksis', function (Blueprint $table) {
            // Boleh kosong (nullable) karena tidak semua orang pakai promo
            $table->string('kode_promo')->nullable()->after('total_harga'); 
            // Default 0 agar transaksi normal tanpa diskon tidak error
            $table->integer('diskon')->default(0)->after('kode_promo'); 
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['kode_promo', 'diskon']);
        });
    }
};
