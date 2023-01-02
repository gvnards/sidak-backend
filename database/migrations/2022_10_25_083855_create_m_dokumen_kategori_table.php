<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDokumenKategoriTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_dokumen_kategori', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->text('nama');
      $table->string('formatNama', 100);
      $table->string('keterangan', 255)->index();
      $table->float('ukuran', 3, 2, true);
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
    Schema::dropIfExists('m_dokumen_kategori');
  }
}
