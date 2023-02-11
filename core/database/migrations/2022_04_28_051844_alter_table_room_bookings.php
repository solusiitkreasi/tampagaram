<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRoomBookings extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('room_bookings', function (Blueprint $table) {
      $table->unsignedDecimal('subtotal', 8, 2)->after('guests');
      $table->unsignedDecimal('discount', 8, 2)->default(0.00)->after('subtotal');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('room_bookings', function (Blueprint $table) {
      $table->dropColumn('subtotal');
      $table->dropColumn('discount');
    });
  }
}
