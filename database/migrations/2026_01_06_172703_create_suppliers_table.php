<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('currency')->default('USD');
            $table->string('payment_terms')->nullable();
            $table->string('supplier_category')->nullable();
            $table->text('capabilities')->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('minimum_order_value')->nullable();
            $table->string('erp_code')->nullable();
            $table->string('sync_status')->default('pending');
            $table->timestamp('last_sync')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
