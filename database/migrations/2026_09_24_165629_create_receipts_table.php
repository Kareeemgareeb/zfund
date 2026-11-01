<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('transactions');
            $table->string('receipt_number')->unique();
            $table->string('pdf_path')->unique();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('receipts'); }
};
