<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPegawaiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_pegawai', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nip', 30)->index();
      $table->text('password');
      $table->unsignedBigInteger('idAppRoleUser');
      $table->foreign('idAppRoleUser')->references('id')->on('m_app_role_user');
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
    Schema::dropIfExists('m_pegawai');
  }
}
