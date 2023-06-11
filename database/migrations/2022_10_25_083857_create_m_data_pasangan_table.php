<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataPasanganTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_pasangan', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->string('tempatLahir', 255);
      $table->date('tanggalLahir')->nullable();
      $table->date('tanggalStatusPerkawinan')->nullable();
      $table->string('nomorDokumen', 255);
      $table->date('tanggalDokumen')->nullable();
      $table->unsignedBigInteger('idStatusPerkawinan');
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataPasanganUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idStatusPerkawinan')->references('id')->on('m_status_perkawinan');
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
    Schema::table('m_data_pasangan', function (Blueprint $table) {
      $table->foreign('idDataPasanganUpdate')->references('id')->on('m_data_pasangan');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_pasangan');
  }
}
