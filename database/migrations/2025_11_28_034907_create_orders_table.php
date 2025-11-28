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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Data Utama Pesanan
            $table->string('order_id')->unique();
            $table->enum('type', ['Dine-in', 'Takeaway', 'Delivery']);
            $table->enum('status', ['Pending', 'Cooking', 'Ready', 'Waiting Runner', 'Completed', 'Cancelled'])->default('Pending');
            $table->string('source')->default('QR Scan');

            // Data Pelanggan
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            
            // Detail Item
            $table->text('items_description');
            $table->decimal('total_amount', 8, 2);
            $table->decimal('runner_fee', 8, 2)->default(0.00);

            // Detail Delivery (Hanya terisi jika type = Delivery)
            $table->text('delivery_address')->nullable();
            $table->enum('runner_option', ['Normal', 'Prioritas'])->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
