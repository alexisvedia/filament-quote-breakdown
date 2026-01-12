<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costsheet_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->integer('version_number');
            $table->json('items_snapshot'); // Snapshot of all items at this version
            $table->decimal('total_cost', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['quote_id', 'supplier_id', 'version_number']);
            $table->index(['quote_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costsheet_versions');
    }
};
