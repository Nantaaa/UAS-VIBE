<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->decimal('harga_asli', 12, 2)->nullable()->after('tarif');
            $table->string('tipe_trip', 40)->default('Open Trip')->after('durasi_hari');
            $table->boolean('is_bestseller')->default(false)->after('is_active');
            $table->string('highlight', 150)->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn(['harga_asli', 'tipe_trip', 'is_bestseller', 'highlight']);
        });
    }
};
