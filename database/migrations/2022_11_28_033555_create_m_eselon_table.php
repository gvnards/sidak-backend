<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMEselonTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_eselon', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 10);
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_eselon');
  }
}
