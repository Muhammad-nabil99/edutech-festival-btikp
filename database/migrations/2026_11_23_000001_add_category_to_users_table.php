<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'category')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('category', 50)->nullable()->after('role');
            });
        }

        // Migrasi data akun juri yang sudah ada secara idempotent:
        // Juri 1 -> gim_edukasi (GIM)
        DB::table('users')
            ->where('username', 'like', '%juri1%')
            ->orWhere('email', 'juri1@edutech.btikp.id')
            ->orWhere('name', 'like', '%Juri 1%')
            ->update(['category' => 'gim_edukasi']);

        // Juri 2 -> gim_edukasi (GIM)
        DB::table('users')
            ->where('username', 'like', '%juri2%')
            ->orWhere('email', 'juri2@edutech.btikp.id')
            ->orWhere('name', 'like', '%Juri 2%')
            ->update(['category' => 'gim_edukasi']);

        // Juri 3 -> media_pembelajaran_interaktif (MPI)
        DB::table('users')
            ->where('username', 'like', '%juri3%')
            ->orWhere('email', 'juri3@edutech.btikp.id')
            ->orWhere('name', 'like', '%Juri 3%')
            ->update(['category' => 'media_pembelajaran_interaktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'category')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};

