<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('no_hp');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('kewarganegaraan', 50)->default('Indonesia')->after('jenis_kelamin');
            $table->string('no_identitas', 30)->nullable()->after('kewarganegaraan');
            $table->string('alamat', 255)->nullable()->after('no_identitas');
            $table->string('kota', 100)->nullable()->after('alamat');
            $table->string('kode_pos', 10)->nullable()->after('kota');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_lahir', 'jenis_kelamin', 'kewarganegaraan',
                'no_identitas', 'alamat', 'kota', 'kode_pos',
            ]);
        });
    }
};
