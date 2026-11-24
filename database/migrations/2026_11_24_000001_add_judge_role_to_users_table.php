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
        if (!Schema::hasColumn('users', 'judge_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('judge_role')->nullable()->after('category');
            });
        }

        // Migrasi peran dewan juri yang sudah ada secara idempotent:
        // Juri 1 -> Peran 1 (Poin 1 dan Poin 3)
        DB::table('users')
            ->where(function ($q) {
                $q->where('username', 'like', '%juri1%')
                  ->orWhere('email', 'juri1@edutech.btikp.id')
                  ->orWhere('name', 'like', '%Juri 1%');
            })
            ->where('role', 'judge')
            ->update(['judge_role' => 1]);

        // Juri 2 -> Peran 2 (Poin 2)
        DB::table('users')
            ->where(function ($q) {
                $q->where('username', 'like', '%juri2%')
                  ->orWhere('email', 'juri2@edutech.btikp.id')
                  ->orWhere('name', 'like', '%Juri 2%');
            })
            ->where('role', 'judge')
            ->update(['judge_role' => 2]);

        // Juri 3 -> Peran 3 (Poin 4)
        DB::table('users')
            ->where(function ($q) {
                $q->where('username', 'like', '%juri3%')
                  ->orWhere('email', 'juri3@edutech.btikp.id')
                  ->orWhere('name', 'like', '%Juri 3%');
            })
            ->where('role', 'judge')
            ->update(['judge_role' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'judge_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('judge_role');
            });
        }
    }
};
