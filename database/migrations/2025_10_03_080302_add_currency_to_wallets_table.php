<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Menambahkan kolom 'currency' setelah kolom 'icon'
            // Tipe VARCHAR(10) cukup untuk kode ISO (misal: "IDR", "USD")
            // Menetapkan nilai default 'IDR' untuk semua data yang sudah ada
            $table->string('currency', 10)->after('icon')->default('USD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Menghapus kolom 'currency' jika migrasi di-rollback
            $table->dropColumn('currency');
        });
    }
};