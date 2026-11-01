<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('charitable_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->enum('category', ['ORPHAN_SPONSORSHIP', 'MARRIAGE_AID', 'MOSQUE_CONSTRUCTION', 'FOOD_BASKET_DISTRIBUTION'])->index();
            $table->decimal('target_budget', 12, 2);
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->decimal('disbursed_amount', 12, 2)->default(0);
            $table->enum('status', ['ACTIVE', 'FULLY_FUNDED', 'COMPLETED', 'PAUSED'])->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('charitable_projects'); }
};
