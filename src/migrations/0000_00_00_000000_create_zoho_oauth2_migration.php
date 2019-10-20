<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZohoOauth2Migration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoho_oauth_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id', 128)->nullable();
            $table->string('client_secret', 128)->nullable();
            $table->string('client_domain', 64)->nullable();
            $table->string('protocol', 12)->nullable();
            $table->string('api_domain', 62)->nullable();
            $table->enum('connect_to', ['sandbox', 'live'])->default('sandbox');
            $table->string('access_token', 191)->nullable();
            $table->string('refresh_token', 191)->nullable();
            $table->string('token_type', 32)->nullable();
            $table->string('expires_in', 32)->nullable();
            $table->string('expires_in_sec', 32)->nullable();
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
        Schema::dropIfExists('zoho_oauth_settings');
    }
}
