<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDaftarPendidikanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_daftar_pendidikan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->unsignedBigInteger('idTingkatPendidikan');
      $table->foreign('idTingkatPendidikan')->references('id')->on('m_tingkat_pendidikan');
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
    Schema::dropIfExists('m_daftar_pendidikan');
  }
}
