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
        if (Schema::hasTable('messenger_group')) {
            $this->down();
        }
        Schema::create('messenger_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_name', 100)->nullable();
            $table->longText('group_avatar')->nullable();
            $table->tinyInteger('type');

            $table->timestamps();
            $table->unsignedInteger('created_user')->nullable();
            $table->unsignedInteger('updated_user')->nullable();

            $table->foreign('created_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('updated_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messenger_group');
    }
};
