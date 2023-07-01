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
        if (Schema::hasTable('group_member')) {
            $this->down();
        }

        Schema::create('group_member', function (Blueprint $table) {
            $table->unsignedInteger('id_group');
            $table->unsignedInteger('id_employee');
            $table->tinyInteger('status');
            $table->string('nick_name', 100);

            $table->timestamps();
            $table->unsignedInteger('created_user')->nullable();
            $table->unsignedInteger('updated_user')->nullable();

            $table->foreign('created_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('updated_user')->references('id')->on('employees')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('id_employee')->references('id')->on('employees');
            $table->foreign('id_group')->references('id')->on('messenger_group');

            $table->primary(['id_group', 'id_employee']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_member');
    }
};
