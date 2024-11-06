<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursement_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->integer('price');
            $table->date('date');
            $table->boolean('documentation_needed')->default(false);
            $table->string('before')->nullable();
            $table->string('after')->nullable();
            $table->string('documentation')->nullable();
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursement_forms');
    }
};
