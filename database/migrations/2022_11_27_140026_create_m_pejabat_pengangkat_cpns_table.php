<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPejabatPengangkatCpnsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_pejabat_pengangkat_cpns', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 100);
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
    Schema::dropIfExists('m_pejabat_pengangkat_cpns');
  }
}
