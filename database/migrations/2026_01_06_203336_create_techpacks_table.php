<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('techpacks', function (Blueprint $table) {
            $table->id();
            $table->string('style_code')->unique();
            $table->string('style_name');
            $table->string('design_image')->nullable();
            $table->string('status')->default('under_review');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('buyer')->nullable();
            $table->string('buyer_department')->nullable();
            $table->string('buyer_style_reference')->nullable();
            $table->string('product_group')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('our_contact')->nullable();
            $table->string('purchase_uom')->default('Pieces');
            $table->string('season')->nullable();
            $table->integer('style_lead_time')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->boolean('style_embellishment')->default(false);
            $table->string('construction')->nullable();
            $table->string('content')->nullable();
            $table->string('weight')->nullable();
            $table->string('dyeing_type')->nullable();
            $table->string('yarn_count')->nullable();
            $table->string('width')->nullable();
            $table->string('fabric_article_code')->nullable();
            $table->text('special_finishes')->nullable();
            $table->json('colors')->nullable();
            $table->string('base_size')->nullable();
            $table->json('sizes')->nullable();
            $table->string('sketch')->nullable();
            $table->string('front_artwork')->nullable();
            $table->string('front_technique')->nullable();
            $table->string('back_artwork')->nullable();
            $table->string('back_technique')->nullable();
            $table->string('sleeve_artwork')->nullable();
            $table->string('sleeve_technique')->nullable();
            $table->string('color')->nullable();
            $table->string('dyed_process')->nullable();
            $table->date('initial_request_date')->nullable();
            $table->date('sms_x_date')->nullable();
            $table->text('sms_comments')->nullable();
            $table->string('pp_sample')->nullable();
            $table->decimal('factory_price', 10, 2)->nullable();
            $table->decimal('profit_margin', 5, 2)->nullable();
            $table->string('wfx_style_code')->nullable();
            $table->string('wfx_id')->nullable();
            $table->timestamp('wfx_last_sync')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('techpacks');
    }
};
