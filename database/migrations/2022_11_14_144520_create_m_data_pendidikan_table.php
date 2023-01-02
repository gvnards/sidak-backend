<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataPendidikanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_pendidikan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJenisPendidikan');
      $table->foreign('idJenisPendidikan')->references('id')->on('m_jenis_pendidikan');
      $table->unsignedBigInteger('idTingkatPendidikan');
      $table->foreign('idTingkatPendidikan')->references('id')->on('m_tingkat_pendidikan');
      $table->unsignedBigInteger('idDaftarPendidikan');
      $table->foreign('idDaftarPendidikan')->references('id')->on('m_daftar_pendidikan');
      $table->string('namaSekolah', 255);
      $table->string('gelarDepan', 255);
      $table->string('gelarBelakang', 255);
      $table->date('tanggalLulus')->nullable();
      $table->year('tahunLulus');
      $table->string('nomorDokumen', 255);
      $table->date('tanggalDokumen')->nullable();
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataPendidikanUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
    });
    Schema::table('m_data_pendidikan', function (Blueprint $table) {
      $table->foreign('idDataPendidikanUpdate')->references('id')->on('m_data_pendidikan');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_pendidikan');
  }
}
