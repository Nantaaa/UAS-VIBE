<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->text('termasuk')->nullable()->after('fasilitas');
            $table->text('tidak_termasuk')->nullable()->after('termasuk');
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn(['termasuk', 'tidak_termasuk']);
        });
    }
};
