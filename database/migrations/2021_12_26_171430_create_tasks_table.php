<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('customer_id')->nullable();
            $table->string('title')->nullable();
            $table->text('detail')->nullable();
            $table->dateTime('due_date')->nullable()->index('DUE_DATE');
            $table->boolean('is_complete')->nullable()->default(false);
            $table->boolean('is_notify')->nullable()->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
