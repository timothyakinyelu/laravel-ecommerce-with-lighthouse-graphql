<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders_table')->onUpdate('cascade')->onDelete('cascade');
            $table->mediumInteger('tracking_number')->nullable();
            $table->decimal('total_weight')->nullable();
            $table->enum('status', ['PENDING', 'SHIPPED', 'DELIVERED'])->default('PENDING');
            $table->dateTime('date_shipped')->nullable();
            $table->dateTime('date_delivered')->nullable();
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
        Schema::dropIfExists('shipments');
    }
}
