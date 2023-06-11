<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDaftarHukumanDisiplinTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_daftar_hukuman_disiplin', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255);
      $table->unsignedBigInteger('idJenisHukumanDisiplin');
      $table->foreign('idJenisHukumanDisiplin')->references('id')->on('m_jenis_hukuman_disiplin');
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
    Schema::dropIfExists('m_daftar_hukuman_disiplin');
  }
}
