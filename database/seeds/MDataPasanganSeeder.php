<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDataPasanganSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [];
    foreach ($data as $key => $value) {
      DB::table('m_data_pasangan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'tempatLahir' => $value['tempatLahir'],
        'tanggalLahir' => $value['tanggalLahir'],
        'tanggalStatusPerkawinan' => $value['tanggalStatusPerkawinan'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'idStatusPerkawinan' => $value['idStatusPerkawinan'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataPasanganUpdate' => $value['idDataPasanganUpdate'],
        'keteranganUsulan' => $value['keteranganUsulan'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
