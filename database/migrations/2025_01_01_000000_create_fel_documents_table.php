<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fel_documents', function(Blueprint $t){
            $t->uuid('id')->primary();
            $t->string('country',2);
            $t->string('doc_type',32);
            $t->string('internal_id')->unique();
            $t->string('issuer_tax_id');
            $t->string('receiver_tax_id');
            $t->string('currency',3)->default('GTQ');
            $t->dateTime('issue_date');
            $t->string('status',20)->default('pending');
            $t->string('provider',32)->nullable();
            $t->string('provider_uuid')->nullable();
            $t->json('sat_mh_stamp')->nullable();
            $t->longText('request_payload')->nullable();
            $t->longText('response_payload')->nullable();
            $t->string('pdf_path')->nullable();
            $t->string('xml_path')->nullable();
            $t->json('meta')->nullable();
            $t->boolean('is_contingency')->default(false);
            $t->string('access_number')->nullable()->index();
            $t->uuid('contingency_id')->nullable();
            $t->string('failure_code')->nullable();
            $t->text('failure_message')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fel_documents');
    }
};