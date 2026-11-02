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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 30)->unique();
            $table->string('full_name', 150);
            $table->string('school_name', 150);
            $table->string('regency_city', 100)->index();
            $table->string('email', 150)->unique();
            $table->string('whatsapp_number', 25);
            $table->enum('learning_category', ['gim_edukasi', 'media_pembelajaran_interaktif'])->index();
            $table->string('profile_photo_path', 255);
            $table->string('media_link', 500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
