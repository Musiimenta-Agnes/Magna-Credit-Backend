<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_application_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->string('kin_name');
            $table->string('kin_contact');
            $table->string('occupation');
            $table->decimal('monthly_income', 15, 2);
            $table->decimal('loan_amount', 15, 2);  // ← NEW
            $table->string('loan_type');
            $table->string('education');
            $table->string('address');
            $table->string('national_id_image');
            $table->json('collateral_images');
            $table->timestamps();

            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_application_details');
    }
};





// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         Schema::create('loan_application_details', function (Blueprint $table) {
//             $table->id();
//             $table->unsignedBigInteger('loan_application_id');
//             $table->string('kin_name');
//             $table->string('kin_contact');
//             $table->string('occupation');
//             $table->decimal('monthly_income', 15, 2);
//             $table->string('loan_type');
//             $table->string('education');
//             $table->string('address');
//             $table->string('national_id_image');
//             $table->json('collateral_images');
//             $table->timestamps();

//             $table->foreign('loan_application_id')
//                   ->references('id')
//                   ->on('loan_applications')
//                   ->onDelete('cascade');
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('loan_application_details');
//     }
// };
