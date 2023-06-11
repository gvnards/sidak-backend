<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MDataPendidikanSeeder extends Seeder
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
      DB::table('m_data_pendidikan')->insert([
        'idJenisPendidikan' => $value['idJenisPendidikan'],
        'idTingkatPendidikan' => $value['idTingkatPendidikan'],
        'idDaftarPendidikan' => $value['idDaftarPendidikan'],
        'namaSekolah' => $value['namaSekolah'],
        'gelarDepan' => $value['gelarDepan'],
        'gelarBelakang' => $value['gelarBelakang'],
        'tanggalLulus' => $value['tanggalLulus'],
        'tahunLulus' => $value['tahunLulus'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataPendidikanUpdate' => $value['idDataPendidikanUpdate'],
        'keteranganUsulan' => $value['keteranganUsulan'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
