<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('other_contact')->nullable();
            $table->string('kin_name')->nullable();
            $table->string('kin_contact')->nullable();
            $table->string('income')->nullable();
            $table->string('current_address')->nullable();
            $table->string('gender')->nullable();
            $table->string('occupation')->nullable();
            $table->string('loan_type')->nullable();
            $table->string('education')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
