<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('bank_accounts', function(Blueprint $t){$t->id();$t->string('bank_name');$t->string('account_holder');$t->string('account_number');$t->string('iban')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('bank_transfer_submissions',function(Blueprint $t){$t->id();$t->foreignId('bank_account_id')->constrained('bank_accounts')->restrictOnDelete();$t->foreignId('charitable_project_id')->nullable()->constrained('charitable_projects')->restrictOnDelete();$t->foreignId('assistance_request_id')->nullable()->constrained('assistance_requests')->restrictOnDelete();$t->foreignId('transaction_id')->nullable()->unique()->constrained('transactions')->nullOnDelete();$t->string('donor_name',150);$t->string('donor_phone',40)->nullable();$t->decimal('amount',12,2);$t->string('transfer_reference',100);$t->unique(['bank_account_id','transfer_reference'],'bank_transfer_reference_unique');$t->string('bank_name_snapshot');$t->string('account_holder_snapshot');$t->string('account_number_snapshot');$t->string('iban_snapshot')->nullable();$t->string('receipt_path');$t->string('status',20)->default('PENDING')->index();$t->text('review_note')->nullable();$t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamp('reviewed_at')->nullable();$t->timestamps();});
  if (DB::getDriverName()==='mysql') DB::statement("ALTER TABLE transactions MODIFY payment_gateway ENUM('SADAD','TADAWUL_CARD','ELECTRONIC_VOUCHER','BANK_TRANSFER') NOT NULL");
 }
 public function down():void {Schema::dropIfExists('bank_transfer_submissions');Schema::dropIfExists('bank_accounts');}
};
