<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('name')->unique();
            $table->mediumText('short_description');
            $table->text('full_description');
            $table->string('sku')->unique();
            $table->string('gtin')->nullable()->unique();
            $table->integer('quantity');
            $table->enum('status', ['VERIFIED', 'UNVERIFIED'])->default('UNVERIFIED');
            $table->boolean('is_featured');
            $table->boolean('allow_reviews');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
