<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookCredentialsToBasicSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->unsignedTinyInteger('facebook_login_status')->default(1)->comment('1 -> active, 0 -> deactive');
      $table->string('facebook_app_id')->nullable();
      $table->string('facebook_app_secret')->nullable();
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
      $table->dropColumn('facebook_login_status');
      $table->dropColumn('facebook_app_id');
      $table->dropColumn('facebook_app_secret');
    });
  }
}
