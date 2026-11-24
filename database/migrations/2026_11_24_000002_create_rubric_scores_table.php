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
        Schema::create('rubric_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('jury_role'); // 1, 2, atau 3
            $table->unsignedTinyInteger('subindicator_index'); // 1..5 untuk J1, 1..4 untuk J2, 1..3 untuk J3
            $table->unsignedTinyInteger('score'); // Skala Likert 1 sampai 5
            $table->timestamps();

            // Tiap subindikator pada karya hanya punya SATU nilai resmi per peran juri
            $table->unique(['registration_id', 'jury_role', 'subindicator_index'], 'unique_work_role_subindicator');
            $table->index(['registration_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubric_scores');
    }
};
