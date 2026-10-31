<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titik_jemput', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jadwal')->constrained('jadwal_keberangkatan')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->time('jam')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->decimal('biaya_tambahan', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->foreignId('id_titik_jemput')->nullable()->after('id_jadwal')
                ->constrained('titik_jemput')->nullOnDelete();
            $table->decimal('biaya_jemput', 12, 2)->default(0)->after('diskon');
        });

        // Backfill: satu titik jemput per jadwal dari data yang sudah ada
        $jadwal = DB::table('jadwal_keberangkatan')->get();
        foreach ($jadwal as $j) {
            $nama = trim(($j->kota_kumpul ?? '').' — '.($j->titik_kumpul ?? 'Titik kumpul'));
            DB::table('titik_jemput')->insert([
                'id_jadwal' => $j->id,
                'nama' => mb_substr($nama !== '' ? $nama : 'Titik kumpul utama', 0, 120),
                'jam' => $j->jam_kumpul,
                'urutan' => 1,
                'biaya_tambahan' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['id_titik_jemput']);
            $table->dropColumn(['id_titik_jemput', 'biaya_jemput']);
        });

        Schema::dropIfExists('titik_jemput');
    }
};
