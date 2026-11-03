<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->decimal('tarif_private', 12, 2)->nullable()->after('tarif');
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn('tarif_private');
        });
    }
};
