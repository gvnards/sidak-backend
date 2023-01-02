<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAppRoleUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_app_role_user', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('nama', 100); // super-admin, admin-bkpsdm, admin-opd, pegawai
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
    Schema::dropIfExists('m_role');
  }
}
