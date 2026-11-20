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
        Schema::create('judge_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->decimal('pedagogical_score', 5, 2)->default(0); // Bobot 30%
            $table->decimal('creativity_score', 5, 2)->default(0);  // Bobot 30%
            $table->decimal('technical_score', 5, 2)->default(0);   // Bobot 25%
            $table->decimal('presentation_score', 5, 2)->default(0);// Bobot 15%
            $table->decimal('total_score', 5, 2)->default(0);       // Nilai akhir berbobot
            $table->text('notes')->nullable();                      // Catatan masukan juri
            $table->timestamps();

            $table->unique(['user_id', 'registration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judge_scores');
    }
};
