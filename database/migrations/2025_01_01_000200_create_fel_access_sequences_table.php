<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fel_access_sequences', function(Blueprint $t){
            $t->id();
            $t->string('country',2);
            $t->string('establishment_code');
            $t->unsignedBigInteger('current_value')->default(0);
            $t->unsignedTinyInteger('digits')->default(18);
            $t->string('reset_policy',10)->default('never');
            $t->timestamps();
            $t->unique(['country','establishment_code']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('fel_access_sequences');
    }
};