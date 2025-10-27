<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('no_shipment', 15)->index();
            $table->string('description', 255);
            $table->enum('status', [1, 2, 3, 4, 5, 6])->default(1); // New, Loading, Delivery, Delivered, Received, Cancelled
            $table->string('status_actor', 50);
            $table->string('location_point', 50);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('note')->nullable();
            $table->string('img_path', 255)->nullable();
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
        Schema::dropIfExists('shipment_ledgers');
    }
}