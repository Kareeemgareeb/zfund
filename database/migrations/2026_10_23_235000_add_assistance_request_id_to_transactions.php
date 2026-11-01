<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('transactions', 'assistance_request_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('assistance_request_id')->nullable()->constrained('assistance_requests')->restrictOnDelete();
            });
        }
    }
    public function down(): void {
        if (Schema::hasColumn('transactions', 'assistance_request_id')) {
            Schema::table('transactions', fn (Blueprint $table) => $table->dropConstrainedForeignId('assistance_request_id'));
        }
    }
};
