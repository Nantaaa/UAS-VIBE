<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_keberangkatan', function (Blueprint $table) {
            $table->string('kota_kumpul', 80)->nullable()->after('tanggal_kembali');
            $table->string('titik_kumpul', 160)->nullable()->after('kota_kumpul');
            $table->time('jam_kumpul')->nullable()->after('titik_kumpul');
            $table->text('catatan_keberangkatan')->nullable()->after('jam_kumpul');
        });

        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->boolean('include_penerbangan')->default(false)->after('tipe_trip');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_keberangkatan', function (Blueprint $table) {
            $table->dropColumn(['kota_kumpul', 'titik_kumpul', 'jam_kumpul', 'catatan_keberangkatan']);
        });

        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn('include_penerbangan');
        });
    }
};
