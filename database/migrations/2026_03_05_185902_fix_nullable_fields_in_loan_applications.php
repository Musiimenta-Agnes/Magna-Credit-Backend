<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('national_id_image')->nullable()->change();
            $table->string('collateral_images')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->timestamp('reviewed_at')->nullable()->change();
            $table->date('disbursement_date')->nullable()->change();
            $table->date('due_date')->nullable()->change();
            $table->text('rejection_reason')->nullable()->change();
            $table->string('occupation')->nullable()->change();
            $table->string('education')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('other_contact')->nullable()->change();
            $table->text('bio_info')->nullable()->change();
            $table->string('kin_name')->nullable()->change();
            $table->string('kin_contact')->nullable()->change();
            $table->string('monthly_income')->nullable()->change();
            $table->string('gender')->nullable()->change();
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign('loan_applications_reviewed_by_foreign');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewed_by')->nullable()->change();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void {}
};
