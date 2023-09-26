<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataAngkaKreditTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_angka_kredit', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idDaftarJenisAngkaKredit')->nullable();
      $table->foreign('idDaftarJenisAngkaKredit')->references('id')->on('m_daftar_jenis_angka_kredit');
      $table->unsignedBigInteger('idDataJabatan')->nullable();
      $table->foreign('idDataJabatan')->references('id')->on('m_data_jabatan');
      $table->year('tahun')->nullable();
      $table->date('periodePenilaianMulai');
      $table->date('periodePenilaianSelesai');
      $table->float('angkaKreditUtama',8,2)->nullable();
      $table->float('angkaKreditPenunjang',8,2)->nullable();
      $table->float('angkaKreditTotal',8,2)->nullable();
      $table->date('tanggalDokumen');
      $table->string('nomorDokumen', 100);
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataAngkaKreditUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_angka_kredit');
  }
}
