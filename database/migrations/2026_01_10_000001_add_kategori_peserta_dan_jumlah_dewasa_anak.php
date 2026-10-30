<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_dewasa')->default(0)->after('jumlah_peserta');
            $table->unsignedSmallInteger('jumlah_anak')->default(0)->after('jumlah_dewasa');
        });

        Schema::table('peserta', function (Blueprint $table) {
            $table->string('kategori', 10)->default('dewasa')->after('nama_peserta');
        });

        // Data lama: seluruh peserta dianggap dewasa
        DB::table('pemesanan')->update(['jumlah_dewasa' => DB::raw('jumlah_peserta')]);
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dewasa', 'jumlah_anak']);
        });
    }
};
