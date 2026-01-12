<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_supplier', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('responded_at');
            $table->text('invitation_message')->nullable()->after('deadline');
        });
    }

    public function down(): void
    {
        Schema::table('quote_supplier', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'invitation_message']);
        });
    }
};
