<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable()->after('nama_peserta');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('no_hp', 20)->nullable()->after('no_identitas');
            $table->string('email', 100)->nullable()->after('no_hp');
            $table->string('alamat', 255)->nullable()->after('email');
            $table->string('kota', 100)->nullable()->after('alamat');
            $table->string('provinsi', 100)->nullable()->after('kota');
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'no_hp',
                'email', 'alamat', 'kota', 'provinsi',
            ]);
        });
    }
};
