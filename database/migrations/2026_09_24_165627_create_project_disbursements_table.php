<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charitable_project_id')->constrained('charitable_projects');
            $table->foreignId('assistance_request_id')->nullable()->constrained('assistance_requests');
            $table->decimal('amount', 12, 2);
            $table->string('voucher_reference')->unique();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('project_disbursements'); }
};
