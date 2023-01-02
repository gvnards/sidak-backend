<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDaftarAlasanHukumanDisiplinTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_daftar_alasan_hukuman_disiplin', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->text('nama');
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
    Schema::dropIfExists('m_daftar_alasan_hukuman_disiplin');
  }
}
