<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDataJabatanMutasiUnorTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_data_jabatan_mutasi_unor', function (Blueprint $table) {
			$table->id()->autoIncrement()->index();
			$table->unsignedBigInteger('idDataJabatan');
			$table->foreign('idDataJabatan')->references('id')->on('m_data_jabatan');
			$table->unsignedBigInteger('idJabatan');
      $table->foreign('idJabatan')->references('id')->on('m_jabatan');
      $table->date('tmt');
      $table->date('spmt');
      $table->date('tanggalDokumen');
      $table->string('nomorDokumen', 100);
      $table->unsignedBigInteger('idDokumen')->nullable();
      $table->foreign('idDokumen')->references('id')->on('m_dokumen');
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
		Schema::dropIfExists('m_data_jabatan_mutasi_unor');
	}
}
