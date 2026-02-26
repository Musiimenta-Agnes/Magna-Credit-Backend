<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('loans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('loan_type')->nullable();
        $table->decimal('monthly_income', 15, 2)->nullable();
        $table->string('next_of_kin_name')->nullable();
        $table->string('next_of_kin_contact')->nullable();
        $table->string('current_address')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

};
