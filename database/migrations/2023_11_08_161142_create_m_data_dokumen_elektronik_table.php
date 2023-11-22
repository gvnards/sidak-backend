<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataDokumenElektronikTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::dropIfExists('m_data_dokumen_elektronik');
    Schema::create('m_data_dokumen_elektronik', function (Blueprint $table) {
      $table->unsignedBigInteger('idPegawai')->index();
      $table->unsignedBigInteger('idDaftarDokEl')->index();
      $table->unsignedBigInteger('idDokumen');
      $table->primary(['idPegawai', 'idDaftarDokEl']);
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idDaftarDokEl')->references('id')->on('m_daftar_dokumen_elektronik');
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->date('tanggalDokumen');
      $table->string('nomorDokumen', 100);
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
    Schema::dropIfExists('m_data_dokumen_elektronik');
  }
}
