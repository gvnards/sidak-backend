<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataCpnsPnsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_cpns_pns', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idPegawai');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
      $table->date('tmtCpns')->nullable();
      $table->date('tglSkCpns')->nullable();
      $table->string('nomorSkCpns', 100);
      $table->date('tglSpmt')->nullable();
      $table->string('nomorSpmt', 100);
      $table->unsignedBigInteger('idPejabatPengangkatCpns');
      $table->foreign('idPejabatPengangkatCpns')->references('id')->on('m_pejabat_pengangkat_cpns');
      $table->unsignedBigInteger('idDokumenSkCpns')->nullable();
      $table->foreign('idDokumenSkCpns')->references('id')->on('m_dokumen');
      $table->date('tmtPns')->nullable();
      $table->date('tglSkPns')->nullable();
      $table->string('nomorSkPns', 100);
      $table->date('tglSttpl')->nullable();
      $table->string('nomorSttpl', 100);
      $table->date('tglSuratDokter')->nullable();
      $table->string('nomorSuratDokter', 100);
      $table->string('nomorKarpeg', 100);
      $table->string('nomorKarisKarsu', 100);
      $table->unsignedBigInteger('idDokumenSkPns')->nullable();
      $table->foreign('idDokumenSkPns')->references('id')->on('m_dokumen');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('m_data_cpns_pns');
  }
}
