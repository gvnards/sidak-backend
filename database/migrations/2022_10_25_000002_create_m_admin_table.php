<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAdminTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_admin', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->string('username', 100)->index();
      $table->text('password');
      $table->string('unitOrganisasi', 100);
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
    Schema::dropIfExists('m_admin');
  }
}
