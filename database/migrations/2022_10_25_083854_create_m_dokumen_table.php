<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDokumenTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_dokumen', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 255)->index();
      $table->mediumText('dokumen');
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
    Schema::dropIfExists('m_dokumen');
  }
}
