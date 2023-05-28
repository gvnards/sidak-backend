<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataJabatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_jabatan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJabatan');
      $table->foreign('idJabatan')->references('id')->on('m_jabatan');
      $table->boolean('isPltPlh');
      $table->unsignedBigInteger('idJabatanTugasTambahan')->nullable();
      $table->foreign('idJabatanTugasTambahan')->references('id')->on('m_jabatan_tugas_tambahan');
      $table->date('tmt');
      $table->date('spmt');
      $table->date('tanggalDokumen');
      $table->string('nomorDokumen', 100);
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataJabatanUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
    });
    Schema::table('m_data_jabatan', function (Blueprint $table) {
      $table->foreign('idDataJabatanUpdate')->references('id')->on('m_data_jabatan');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_jabatan');
  }
}
