<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('zakat_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained('donors');
            $table->enum('calculation_type', ['GOLD_SILVER', 'LIVESTOCK', 'CROPS_AGRICULTURE', 'CASH_WEALTH'])->index();
            $table->json('input_parameters');
            $table->decimal('computed_zakat_due', 12, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('zakat_calculations'); }
};
