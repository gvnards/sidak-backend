<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataPangkatTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_pangkat', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJenisPangkat');
      $table->foreign('idJenisPangkat')->references('id')->on('m_jenis_pangkat');
      $table->unsignedBigInteger('idDaftarPangkat');
      $table->foreign('idDaftarPangkat')->references('id')->on('m_daftar_pangkat');
      $table->integer('masaKerjaTahun');
      $table->integer('masaKerjaBulan');
      $table->string('nomorDokumen', 255);
      $table->date('tanggalDokumen')->nullable();
      $table->date('tmt')->nullable();
      $table->string('nomorBkn', 255);
      $table->date('tanggalBkn')->nullable();
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataPangkatUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
    });
    Schema::table('m_data_pangkat', function (Blueprint $table) {
      $table->foreign('idDataPangkatUpdate')->references('id')->on('m_data_pangkat');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_pangkat');
  }
}
