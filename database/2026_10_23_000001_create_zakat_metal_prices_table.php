<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('zakat_metal_prices', function (Blueprint $table) {
  $table->id(); $table->date('price_date')->unique(); $table->decimal('gold_price_per_gram',14,4);
  $table->decimal('silver_price_per_gram',14,4); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('zakat_metal_prices'); }
};
