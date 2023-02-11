<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageCouponsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('package_coupons', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable();
      $table->string('code')->nullable();
      $table->string('type')->nullable();
      $table->unsignedDecimal('value', 8, 2)->nullable();
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->unsignedInteger('serial_number')->nullable();
      $table->text('packages')->nullable();
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
    Schema::dropIfExists('package_coupons');
  }
}
