<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableBasicSettings extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->string('qr_image')->nullable();
      $table->string('qr_color')->default('000000');
      $table->unsignedInteger('qr_size')->default(250);
      $table->string('qr_style')->default('square');
      $table->string('qr_eye_style')->default('square');
      $table->unsignedInteger('qr_margin')->default(0);
      $table->string('qr_text')->nullable();
      $table->string('qr_text_color')->default('000000');
      $table->unsignedInteger('qr_text_size')->default(15);
      $table->unsignedInteger('qr_text_x')->default(50);
      $table->unsignedInteger('qr_text_y')->default(50);
      $table->string('qr_inserted_image')->nullable();
      $table->unsignedInteger('qr_inserted_image_size')->default(20);
      $table->unsignedInteger('qr_inserted_image_x')->default(50);
      $table->unsignedInteger('qr_inserted_image_y')->default(50);
      $table->string('qr_type')->default('default')->comment('default, image, text');
      $table->string('qr_url')->nullable();
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
      $table->dropColumn('qr_image');
      $table->dropColumn('qr_color');
      $table->dropColumn('qr_size');
      $table->dropColumn('qr_style');
      $table->dropColumn('qr_eye_style');
      $table->dropColumn('qr_margin');
      $table->dropColumn('qr_text');
      $table->dropColumn('qr_text_color');
      $table->dropColumn('qr_text_size');
      $table->dropColumn('qr_text_x');
      $table->dropColumn('qr_text_y');
      $table->dropColumn('qr_inserted_image');
      $table->dropColumn('qr_inserted_image_size');
      $table->dropColumn('qr_inserted_image_x');
      $table->dropColumn('qr_inserted_image_y');
      $table->dropColumn('qr_type');
      $table->dropColumn('qr_url');
    });
  }
}
