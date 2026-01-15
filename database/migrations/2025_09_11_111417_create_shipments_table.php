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
            $table->string('no_shipment', 15)->unique();
            $table->string('description', 255);
            $table->bigInteger('category_id')->index();
            $table->string('sender', 50)->index();
            $table->string('sender_pic', 50);
            $table->string('destination', 50)->index();
            $table->string('destination_pic', 50)->nullable();
            $table->enum('handling_level', ['1', '2']); // Normal, Fragile
            $table->enum('shipment_by', ['1', '2', '3']); // Personally, Shuttle, Messenger
            $table->string('agent', 50)->nullable();
            $table->enum('is_branch', ['0', '1']); // No, Yes
            $table->string('packing', 50)->nullable();
            $table->string('dc_support', 50)->nullable();
            $table->enum('status', [1, 2, 3, 4, 5, 6])->default(1); // New, Loading, Delivery, Delivered, Cancelled
            $table->string('img_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by', 40);
            $table->string('updated_by', 40);
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