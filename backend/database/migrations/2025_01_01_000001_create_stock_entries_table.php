<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('stock_no')->unique()->nullable();
            $table->string('item_code', 100);
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('location')->nullable();
            $table->unsignedInteger('store_id');
            $table->date('in_stock_date');
            $table->enum('status', ['pending', 'in_stock'])->default('pending');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}