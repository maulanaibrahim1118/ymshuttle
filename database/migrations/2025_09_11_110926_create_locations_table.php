<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('site', 8)->unique();
            $table->string('initial', 5)->unique();
            $table->string('name', 50)->unique();
            $table->string('wilayah', 50);
            $table->string('regional', 50);
            $table->string('area', 50);
            $table->string('address', 255);
            $table->string('city', 50);
            $table->string('email', 255)->nullable();
            $table->string('dc_support', 50)->nullable();
            $table->string('telp', 20)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
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
        Schema::dropIfExists('locations');
    }
}