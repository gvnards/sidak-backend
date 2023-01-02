<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataHukumanDisiplinTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_hukuman_disiplin', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJenisHukumanDisiplin');
      $table->foreign('idJenisHukumanDisiplin')->references('id')->on('m_jenis_hukuman_disiplin');
      $table->unsignedBigInteger('idDaftarHukumanDisiplin');
      $table->foreign('idDaftarHukumanDisiplin')->references('id')->on('m_daftar_hukuman_disiplin');
      $table->string('nomorDokumen');
      $table->date('tanggalDokumen')->nullable();
      $table->date('tmtAwal')->nullable();
      $table->integer('masaHukuman');
      $table->date('tmtAkhir')->nullable();
      $table->unsignedBigInteger('idDaftarDasarHukumHukdis');
      $table->foreign('idDaftarDasarHukumHukdis')->references('id')->on('m_daftar_dasar_hukum_hukuman_disiplin');
      $table->unsignedBigInteger('idDaftarAlasanHukdis');
      $table->foreign('idDaftarAlasanHukdis')->references('id')->on('m_daftar_alasan_hukuman_disiplin');
      $table->string('keteranganAlasanHukdis', 255);
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataHukumanDisiplinUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
    });
    Schema::table('m_data_hukuman_disiplin', function (Blueprint $table) {
      $table->foreign('idDataHukumanDisiplinUpdate')->references('id')->on('m_data_hukuman_disiplin');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_hukuman_disiplin');
  }
}
