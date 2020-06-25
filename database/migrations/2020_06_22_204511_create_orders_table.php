<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('shipping_address_id')->nullable()->constrained('shipping_addresses')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('billing_address_id')->nullable()->constrained('billing_addresses')->onUpdate('cascade')->onDelete('set null');
            $table->string('customer_ip')->nullable();
            $table->string('payment_status')->nullable();
            $table->enum('status', ['PENDING', 'COMPLETED'])->default('PENDING');
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
        Schema::dropIfExists('orders');
    }
}
