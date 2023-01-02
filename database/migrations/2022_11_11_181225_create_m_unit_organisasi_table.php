<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMUnitOrganisasiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_unit_organisasi', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->string('kodeKomponen', 50)->index();
      $table->boolean('digunakanSotkSekarang');
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
    Schema::dropIfExists('m_unit_organisasi');
  }
}
