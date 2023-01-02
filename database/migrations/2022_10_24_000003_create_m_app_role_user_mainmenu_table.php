<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAppRoleUserMainmenuTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('m_app_role_user_mainmenu', function (Blueprint $table) {
      $table->id()->autoIncrement()->index();
      $table->unsignedBigInteger('idAppRoleUser');
      $table->unsignedBigInteger('idAppMainmenu');
      $table->foreign('idAppRoleUser')->references('id')->on('m_app_role_user');
      $table->foreign('idAppMainmenu')->references('id')->on('m_app_mainmenu');
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
    Schema::dropIfExists('m_app_role_user_mainmenu');
  }
}
