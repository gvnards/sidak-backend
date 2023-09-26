<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDaftarJenisAngkaKreditTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_daftar_jenis_angka_kredit', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('jenisAngkaKredit', 255);
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
    Schema::dropIfExists('m_daftar_jenis_angka_kredit');
  }
}
