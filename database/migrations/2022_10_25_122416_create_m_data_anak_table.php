<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataAnakTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_anak', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->string('tempatLahir', 255);
      $table->date('tanggalLahir')->nullable()->nullable();
      $table->string('nomorDokumen', 255);
      $table->date('tanggalDokumen')->nullable()->nullable();
      $table->unsignedBigInteger('idOrangTua');
      $table->unsignedBigInteger('idStatusAnak');
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataAnakUpdate')->nullable();
      $table->foreign('idOrangTua')->references('id')->on('m_data_pasangan');
      $table->foreign('idStatusAnak')->references('id')->on('m_status_anak');
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
    Schema::table('m_data_anak', function (Blueprint $table) {
      $table->foreign('idDataAnakUpdate')->references('id')->on('m_data_anak');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_anak');
  }
}
