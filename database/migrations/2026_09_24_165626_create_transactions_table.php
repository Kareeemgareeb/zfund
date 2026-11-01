<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ref', 40)->unique();
            $table->foreignId('donor_id')->constrained('donors');
            $table->foreignId('charitable_project_id')->nullable()->constrained('charitable_projects');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_gateway', ['SADAD', 'TADAWUL_CARD', 'ELECTRONIC_VOUCHER'])->index();
            $table->enum('status', ['PENDING', 'SUCCESSFUL', 'FAILED'])->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('transactions'); }
};
