<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_identitas', 20)->default('KTP')->after('kewarganegaraan');
            $table->string('file_identitas')->nullable()->after('no_identitas');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_identitas', 'file_identitas']);
        });
    }
};
