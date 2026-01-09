<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('version'); // v1, v2, etc
            $table->enum('state', ['current', 'historic'])->default('current');
            $table->string('archive_path')->nullable(); // PDF file path
            $table->string('archive_name')->nullable(); // PO-834893-v2.pdf
            $table->datetime('loading_date');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
