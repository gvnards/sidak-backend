<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMKelasJabatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_kelas_jabatan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama')->index();
      $table->unsignedBigInteger('idUangKinerja');
      $table->foreign('idUangKinerja')->references('id')->on('m_uang_kinerja');
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
    Schema::dropIfExists('m_kelas_jabatan');
  }
}
