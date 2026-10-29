<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_promo', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->enum('tipe', ['persen', 'nominal'])->default('persen');
            $table->decimal('nilai', 12, 2);
            $table->decimal('min_transaksi', 12, 2)->default(0);
            $table->decimal('maks_diskon', 12, 2)->nullable();
            $table->unsignedInteger('kuota')->nullable();
            $table->unsignedInteger('terpakai')->default(0);
            $table->boolean('aktif')->default(true);
            $table->date('berlaku_hingga')->nullable();
            $table->timestamps();
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->string('kode_promo', 30)->nullable()->after('total_bayar');
            $table->decimal('diskon', 12, 2)->default(0)->after('kode_promo');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn(['kode_promo', 'diskon']);
        });
        Schema::dropIfExists('kode_promo');
    }
};
