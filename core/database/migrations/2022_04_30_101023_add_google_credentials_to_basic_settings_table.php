<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleCredentialsToBasicSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->unsignedTinyInteger('google_login_status')->default(1)->comment('1 -> active, 0 -> deactive');
      $table->string('google_client_id')->nullable();
      $table->string('google_client_secret')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->dropColumn('google_login_status');
      $table->dropColumn('google_client_id');
      $table->dropColumn('google_client_secret');
    });
  }
}
