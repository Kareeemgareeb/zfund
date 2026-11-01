<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users');
            $table->string('donor_name', 150)->default('???? ???');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('donors'); }
};
