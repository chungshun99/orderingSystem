<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('table_id');
            $table->string('table_name');
            $table->integer('user_id');
            $table->string('user_name');
            $table->decimal('total_price') -> default(0);
            $table->decimal('total_received') -> default(0);
            $table->decimal('change') -> default(0);
            $table->string('payment_type') -> default("");
            $table->string('sales_status') -> default("unpaid");
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
        Schema::dropIfExists('sales');
    }
}
