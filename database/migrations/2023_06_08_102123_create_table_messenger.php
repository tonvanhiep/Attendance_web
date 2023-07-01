<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('messenger')) {
            $this->down();
        }

        Schema::create('messenger', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_sender');
            $table->unsignedInteger('id_receiver');
            $table->longText('content');
            $table->unsignedInteger('reply')->nullable();
            $table->tinyInteger('status');

            $table->timestamps();
            $table->unsignedInteger('created_user')->nullable();
            $table->unsignedInteger('updated_user')->nullable();

            $table->foreign('created_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('updated_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('id_receiver')->references('id')->on('messenger_group');
            $table->foreign('id_sender')->references('id')->on('employees');
            $table->foreign('reply')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messenger');
    }
};
