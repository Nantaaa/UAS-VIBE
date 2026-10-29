<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_destinasi', 100);
            $table->string('lokasi', 150);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('paket_wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_destinasi')->constrained('destinasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_paket', 150);
            $table->text('deskripsi')->nullable();
            $table->text('fasilitas')->nullable();
            $table->text('itinerary')->nullable();
            $table->unsignedInteger('durasi_hari')->default(1);
            $table->decimal('tarif', 12, 2);
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('jadwal_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paket')->constrained('paket_wisata')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali')->nullable();
            $table->unsignedInteger('kuota_peserta');
            $table->unsignedInteger('sisa_kuota');
            $table->timestamps();
            $table->unique(['id_paket', 'tanggal_berangkat']);
        });

        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_jadwal')->constrained('jadwal_keberangkatan')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('kode_pemesanan', 20)->unique();
            $table->unsignedInteger('jumlah_peserta');
            $table->decimal('total_bayar', 12, 2);
            $table->enum('status_pemesanan', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'perlu_perbaikan',
                'terkonfirmasi',
                'kedaluwarsa',
                'dibatalkan',
            ])->default('menunggu_pembayaran');
            $table->text('catatan_status')->nullable();
            $table->dateTime('tanggal_pemesanan');
            $table->dateTime('batas_pembayaran');
            $table->timestamps();
            $table->index(['status_pemesanan', 'batas_pembayaran']);
        });

        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemesanan')->constrained('pemesanan')->cascadeOnDelete();
            $table->string('nama_peserta', 100);
            $table->enum('jenis_identitas', ['KTP', 'Paspor'])->default('KTP');
            $table->string('no_identitas', 30);
            $table->string('file_identitas')->nullable();
            $table->enum('status', ['menunggu', 'valid', 'perlu_perbaikan'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemesanan')->constrained('pemesanan')->cascadeOnDelete();
            $table->foreignId('id_admin_verifikator')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('jumlah_bayar', 12, 2);
            $table->string('bukti_pembayaran');
            $table->dateTime('tanggal_upload');
            $table->enum('status_verifikasi', ['menunggu', 'valid', 'tidak_valid'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('e_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemesanan')->constrained('pemesanan')->cascadeOnDelete();
            $table->string('kode_qr', 100)->unique();
            $table->string('file_pdf')->nullable();
            $table->dateTime('tanggal_terbit');
            $table->timestamps();
        });

        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_petugas', 100);
            $table->string('kontak', 15);
            $table->enum('peran', ['guide', 'driver'])->default('guide');
            $table->timestamps();
        });

        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jadwal')->constrained('jadwal_keberangkatan')->cascadeOnDelete();
            $table->foreignId('id_petugas')->constrained('petugas')->cascadeOnDelete();
            $table->date('tanggal_tugas');
            $table->timestamps();
            $table->unique(['id_jadwal', 'id_petugas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan');
        Schema::dropIfExists('petugas');
        Schema::dropIfExists('e_ticket');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('peserta');
        Schema::dropIfExists('pemesanan');
        Schema::dropIfExists('jadwal_keberangkatan');
        Schema::dropIfExists('paket_wisata');
        Schema::dropIfExists('destinasi');
    }
};
