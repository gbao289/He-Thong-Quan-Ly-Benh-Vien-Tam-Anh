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
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->string('patient_code', 30)->unique();
            $table->string('full_name', 100);
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone', 20)->index();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('identity_card', 20)->nullable();
            $table->string('insurance_code', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};
