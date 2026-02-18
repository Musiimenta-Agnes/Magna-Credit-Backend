<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // foreign key
            $table->string('name');
            $table->string('contact', 50);
            $table->string('email')->unique();
            $table->text('bio_info')->nullable();
            $table->string('location');
            $table->string('other_contact', 50)->nullable();
            $table->string('gender');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
