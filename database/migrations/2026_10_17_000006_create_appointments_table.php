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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code', 30)->unique();
            $table->foreignId('patient_profile_id')->constrained('patient_profiles')->onDelete('restrict');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('restrict');
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('restrict');
            $table->date('appointment_date');
            $table->enum('shift', ['morning', 'afternoon']);
            $table->time('appointment_time')->nullable();
            $table->text('symptom_description');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date', 'shift'], 'app_doc_date_shift_idx');
            $table->index(['patient_profile_id', 'status'], 'app_patient_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
