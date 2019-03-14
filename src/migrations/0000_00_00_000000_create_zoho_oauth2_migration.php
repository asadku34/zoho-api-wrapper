<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('client_domain')->nullable();
            $table->string('api_domain')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('expires_in_sec')->nullable();
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
