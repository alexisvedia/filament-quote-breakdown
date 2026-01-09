<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->enum('landmark', ['yarn', 'fabric', 'cut', 'sewing', 'washing', 'finishing', 'packaging', 'shipping']);
            $table->date('plan_date')->nullable(); // TNA
            $table->date('real_date')->nullable(); // WIP
            $table->enum('state', ['pending', 'completed', 'delayed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_plan_items');
    }
};
