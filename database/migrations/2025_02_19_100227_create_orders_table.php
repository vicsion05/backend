<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('shipping_address');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cod', 'bank', 'momo', 'vnpay'])->default('cod');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};

