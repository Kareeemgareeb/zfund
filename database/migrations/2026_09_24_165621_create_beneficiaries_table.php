<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->string('file_number', 30)->unique();
            $table->string('full_name', 150);
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->date('birth_date');
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'WIDOWED', 'DIVORCED']);
            $table->unsignedInteger('family_members_count')->default(1);
            $table->string('city', 50)->default('Sirte');
            $table->string('address', 255);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 34)->nullable();
            $table->enum('eligibility_status', ['PENDING', 'VERIFIED', 'INELIGIBLE'])->default('PENDING')->index();
            $table->date('next_renewal_due')->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('beneficiaries'); }
};
