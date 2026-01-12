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
        Schema::create('quote_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->text('body');
            $table->boolean('is_from_wts')->default(true);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['quote_id', 'supplier_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_messages');
    }
};
