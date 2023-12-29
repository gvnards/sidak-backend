<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataSkp2022Table extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_skp_2022', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->year('tahun');
      $table->unsignedBigInteger('idPerilakuKerja');
      $table->foreign('idPerilakuKerja')->references('id')->on('m_daftar_nilai_kerja_kinerja');
      $table->unsignedBigInteger('idHasilKinerja');
      $table->foreign('idHasilKinerja')->references('id')->on('m_daftar_nilai_kerja_kinerja');
      $table->unsignedBigInteger('idKuadranKinerja');
      $table->foreign('idKuadranKinerja')->references('id')->on('m_daftar_nilai_kuadran');
      $table->string('nipNrpPejabatPenilai', 100);
      $table->string('namaPejabatPenilai', 255);
      $table->unsignedBigInteger('idStatusPejabatPenilai');
      $table->foreign('idStatusPejabatPenilai')->references('id')->on('m_status_pejabat_atasan_penilai');
      $table->string('unitOrganisasiPejabatPenilai', 255);
      $table->string('jabatanPejabatPenilai', 255);
      $table->unsignedBigInteger('idGolonganPejabatPenilai')->nullable();
      $table->foreign('idGolonganPejabatPenilai')->references('id')->on('m_daftar_pangkat');
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataSkp2022Update')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
    Schema::table('m_data_skp_2022', function (Blueprint $table) {
      $table->foreign('idDataSkp2022Update')->references('id')->on('m_data_skp_2022');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_skp_2022');
  }
}
