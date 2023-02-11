<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePackageBookings extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('package_bookings', function (Blueprint $table) {
      $table->unsignedDecimal('subtotal', 8, 2)->after('visitors');
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
    Schema::table('package_bookings', function (Blueprint $table) {
      $table->dropColumn('subtotal');
      $table->dropColumn('discount');
    });
  }
}
