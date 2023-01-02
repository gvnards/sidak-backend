<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMStatusPejabatAtasanPenilaiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_status_pejabat_atasan_penilai', function (Blueprint $table) {
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
    Schema::dropIfExists('m_status_pejabat_atasan_penilai');
  }
}
