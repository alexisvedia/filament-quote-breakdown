<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quote_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['quote_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_supplier');
    }
};
