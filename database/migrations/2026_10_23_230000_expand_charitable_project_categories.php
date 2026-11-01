<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Preserve existing category values and records; allow additional category codes.
        Schema::table('charitable_projects', function (Blueprint $table) {
            $table->string('category', 60)->change();
        });
    }
    public function down(): void {
        // Intentionally no reverse: old enum would reject newer category values.
    }
};
