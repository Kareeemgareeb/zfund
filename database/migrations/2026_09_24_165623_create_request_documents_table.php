<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('request_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistance_request_id')->constrained('assistance_requests')->cascadeOnDelete();
            $table->enum('document_type', ['NATIONAL_ID', 'FAMILY_BOOK', 'SALARY_STATEMENT', 'MEDICAL_REPORT', 'DEATH_CERT', 'OTHER']);
            $table->string('file_path', 500);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('request_documents'); }
};
