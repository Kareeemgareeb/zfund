<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 30)->unique();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries');
            $table->enum('category', ['ORPHAN_SPONSORSHIP', 'MARRIAGE_AID', 'FOOD_BASKET', 'MEDICAL_AID', 'EMERGENCY_DEBT', 'GENERAL_RELIEF'])->index();
            $table->decimal('requested_amount', 10, 2);
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->enum('status', ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'REJECTED', 'DISBURSED'])->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('assistance_requests'); }
};
