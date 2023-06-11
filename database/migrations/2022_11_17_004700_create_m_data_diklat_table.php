<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataDiklatTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_diklat', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJenisDiklat');
      $table->foreign('idJenisDiklat')->references('id')->on('m_jenis_diklat');
      $table->unsignedBigInteger('idDaftarDiklat');
      $table->foreign('idDaftarDiklat')->references('id')->on('m_daftar_diklat');
      $table->string('namaDiklat');
      $table->integer('lamaDiklat');
      $table->date('tanggalDiklat')->nullable();
      $table->unsignedBigInteger('idDaftarInstansiDiklat');
      $table->foreign('idDaftarInstansiDiklat')->references('id')->on('m_daftar_instansi_diklat');
      $table->string('institusiPenyelenggara', 255);
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataDiklatUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
    Schema::table('m_data_diklat', function (Blueprint $table) {
      $table->foreign('idDataDiklatUpdate')->references('id')->on('m_data_diklat');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_diklat');
  }
}
