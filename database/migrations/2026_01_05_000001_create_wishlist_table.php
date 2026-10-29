<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_paket')->constrained('paket_wisata')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['id_user', 'id_paket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};
