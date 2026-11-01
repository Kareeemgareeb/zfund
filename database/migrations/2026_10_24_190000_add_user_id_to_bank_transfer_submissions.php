<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('bank_transfer_submissions',function(Blueprint $t){$t->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();$t->index(['user_id','status']);}); }
 public function down(): void {Schema::table('bank_transfer_submissions',function(Blueprint $t){$t->dropForeign(['user_id']);$t->dropIndex(['user_id','status']);$t->dropColumn('user_id');});}
};