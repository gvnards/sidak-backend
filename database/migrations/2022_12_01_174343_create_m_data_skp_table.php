<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataSkpTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_skp', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idJenisJabatan');
      $table->foreign('idJenisJabatan')->references('id')->on('m_jenis_jabatan');
      $table->year('tahun');
      $table->unsignedBigInteger('idJenisPeraturanKinerja');
      $table->foreign('idJenisPeraturanKinerja')->references('id')->on('m_jenis_peraturan_kinerja');
      $table->float('nilaiSkp', 5, 2, true);
      $table->float('orientasiPelayanan', 5, 2, true);
      $table->float('integritas', 5, 2, true);
      $table->float('komitmen', 5, 2, true);
      $table->float('disiplin', 5, 2, true);
      $table->float('kerjaSama', 5, 2, true);
      $table->float('kepemimpinan', 5, 2, true);
      $table->float('nilaiPrestasiKerja', 5, 2, true);
      $table->float('nilaiKonversi', 5, 2, true);
      $table->float('nilaiIntegrasi', 5, 2, true);
      $table->unsignedBigInteger('idStatusPejabatPenilai');
      $table->foreign('idStatusPejabatPenilai')->references('id')->on('m_status_pejabat_atasan_penilai');
      $table->string('nipNrpPejabatPenilai', 100);
      $table->string('namaPejabatPenilai', 255);
      $table->string('jabatanPejabatPenilai', 255);
      $table->string('unitOrganisasiPejabatPenilai', 255);
      $table->string('golonganPejabatPenilai', 50);
      $table->date('tmtGolonganPejabatPenilai')->nullable();
      $table->unsignedBigInteger('idStatusAtasanPejabatPenilai');
      $table->foreign('idStatusAtasanPejabatPenilai')->references('id')->on('m_status_pejabat_atasan_penilai');
      $table->string('nipNrpAtasanPejabatPenilai', 100);
      $table->string('namaAtasanPejabatPenilai', 255);
      $table->string('jabatanAtasanPejabatPenilai', 255);
      $table->string('unitOrganisasiAtasanPejabatPenilai', 255);
      $table->string('golonganAtasanPejabatPenilai', 50);
      $table->date('tmtGolonganAtasanPejabatPenilai')->nullable();
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->unsignedBigInteger('idPegawai');
      $table->unsignedBigInteger('idUsulan');
      $table->unsignedBigInteger('idUsulanStatus');
      $table->unsignedBigInteger('idUsulanHasil');
      $table->unsignedBigInteger('idDataSkpUpdate')->nullable(); // jika ada update, maka refer id ke primary key
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->foreign('idUsulan')->references('id')->on('m_usulan');
      $table->foreign('idUsulanStatus')->references('id')->on('m_usulan_status');
      $table->foreign('idUsulanHasil')->references('id')->on('m_usulan_hasil');
      $table->text('keteranganUsulan');
      $table->timestamps();
      $table->string('idBkn')->default('');
    });
    Schema::table('m_data_skp', function (Blueprint $table) {
      $table->foreign('idDataSkpUpdate')->references('id')->on('m_data_skp');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_skp');
  }
}
