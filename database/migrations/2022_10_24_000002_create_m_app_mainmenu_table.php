<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAppMainmenuTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_app_mainmenu', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 100); // dashboard, pegawai, jabatan, unit organisasi, usulan, logout
      $table->string('icon', 100);
      $table->integer('order');
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
    Schema::dropIfExists('m_app_mainmenu');
  }
}
