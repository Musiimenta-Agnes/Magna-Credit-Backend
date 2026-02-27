<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old split tables if they exist
        Schema::dropIfExists('loan_application_details');
        Schema::dropIfExists('loan_applications');

        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();

            // Foreign key to users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ── Page 1: Personal Information ──
            $table->string('name');
            $table->string('contact', 50);
            $table->string('email');                    // removed unique so same user can re-apply
            $table->text('bio_info')->nullable();
            $table->string('location');
            $table->string('other_contact', 50)->nullable();
            $table->string('gender');                   // Male | Female

            // ── Page 2: Employment & Loan Details ──
            $table->string('kin_name');
            $table->string('kin_contact', 50);
            $table->string('occupation');
            $table->decimal('monthly_income', 15, 2);
            $table->decimal('loan_amount', 15, 2);
            $table->string('loan_type');
            $table->string('education');
            $table->string('address');

            // ── Page 2: Documents ──
            $table->string('national_id_image');
            $table->json('collateral_images')->nullable();

            // ── Status (for admin management) ──
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};