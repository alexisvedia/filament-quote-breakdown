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
        Schema::table('quotes', function (Blueprint $table) {
                        $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                                    $table->foreignId('techpack_id')->nullable()->constrained()->nullOnDelete();
                                                $table->string('season')->nullable();
                                                            $table->date('date')->nullable();
                                                                        $table->date('delivery_date')->nullable();
                                                                                    $table->decimal('fob_price', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
                        $table->dropForeign(['client_id']);
                                    $table->dropForeign(['techpack_id']);
                                                $table->dropColumn(['client_id', 'techpack_id', 'season', 'date', 'delivery_date', 'fob_price']);
        });
    }
};
