<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataPribadiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_data_pribadi', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->string('tempatLahir', 255);
      $table->date('tanggalLahir')->nullable();
      $table->text('alamat');
      $table->string('ktp', 100);
      $table->string('nomorHp', 100);
      $table->string('email', 255);
      $table->string('npwp', 100);
      $table->string('bpjs', 100);
      $table->unsignedBigInteger('idPegawai');
      $table->foreign('idPegawai')->references('id')->on('m_pegawai');
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
    Schema::dropIfExists('m_data_pribadi');
  }
}
