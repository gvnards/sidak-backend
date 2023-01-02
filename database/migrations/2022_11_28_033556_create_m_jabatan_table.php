<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMJabatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_jabatan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->integer('kebutuhan');
      $table->unsignedBigInteger('idKelasJabatan');
      $table->foreign('idKelasJabatan')->references('id')->on('m_kelas_jabatan');
      $table->float('target', 6, 2);
      $table->string('kodeKomponen')->index();
      $table->unsignedBigInteger('idJenisJabatan');
      $table->foreign('idJenisJabatan')->references('id')->on('m_jenis_jabatan');
      $table->unsignedBigInteger('idEselon')->nullable();
      $table->foreign('idEselon')->references('id')->on('m_eselon');
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
    Schema::dropIfExists('m_jabatan');
  }
}
