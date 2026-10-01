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
    Schema::table('menus', function (Blueprint $table) {
        // Menambahkan kolom jenis_menu dengan nilai bawaan 'Makanan'
        // Nilai bawaan ini wajib agar data lama di SQLite tidak error
        $table->string('jenis_menu')->default('Makanan');
    });
}

public function down()
{
    Schema::table('menus', function (Blueprint $table) {
        // Menghapus kolom jika migration dibatalkan
        $table->dropColumn('jenis_menu');
    });
}
};
