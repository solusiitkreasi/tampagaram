<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrCodesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('qr_codes', function (Blueprint $table) {
      $table->id();
      $table->string('name')->default(NULL);
      $table->string('url')->default(NULL);
      $table->string('image')->default(NULL);
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
    Schema::dropIfExists('qr_codes');
  }
}
