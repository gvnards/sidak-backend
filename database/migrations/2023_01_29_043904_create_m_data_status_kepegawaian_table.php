<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataStatusKepegawaianTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_status_kepegawaian', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idDaftarStatusKepegawaian');
      $table->date('tmt')->nullable();
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idDaftarStatusKepegawaian')->references('id')->on('m_daftar_status_kepegawaian');
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
    Schema::dropIfExists('m_data_status_kepegawaian');
  }
}
