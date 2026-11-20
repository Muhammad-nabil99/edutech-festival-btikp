<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('is_curated_top10')->default(false)->index()->after('media_link');
            $table->timestamp('curated_at')->nullable()->after('is_curated_top10');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['is_curated_top10']);
            $table->dropColumn(['is_curated_top10', 'curated_at']);
        });
    }
};
