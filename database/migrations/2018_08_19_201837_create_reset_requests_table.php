<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResetRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reset_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pass')->unique();
            $table->string('email');
            $table->string('userAgent');
            $table->string('ipaddress');
            $table->boolean('completed')->default(false);
            $table->string('usernameModifications')->nullable();
            $table->longText('user')->nullable();
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
        Schema::dropIfExists('reset_requests');
    }
}