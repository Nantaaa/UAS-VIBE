<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE peserta MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peserta MODIFY COLUMN status ENUM('menunggu', 'valid', 'perlu_perbaikan') NOT NULL DEFAULT 'menunggu'");
    }
};
