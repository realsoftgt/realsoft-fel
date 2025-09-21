<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fel_contingencies', function(Blueprint $t){
            $t->uuid('id')->primary();
            $t->string('establishment_code');
            $t->dateTime('started_at');
            $t->dateTime('ended_at')->nullable();
            $t->string('reason')->nullable();
            $t->string('status',20)->default('open');
            $t->unsignedInteger('docs_count')->default(0);
            $t->string('notice_channel',20)->nullable();
            $t->json('notice_payload')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fel_contingencies');
    }
};