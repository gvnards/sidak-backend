<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDataPangkatSeeder extends Seeder
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
      DB::table('m_data_pangkat')->insert([
        'id' => $value['id'],
        'idJenisPangkat' => $value['idJenisPangkat'],
        'idDaftarPangkat' => $value['idDaftarPangkat'],
        'masaKerjaTahun' => $value['masaKerjaTahun'],
        'masaKerjaBulan' => $value['masaKerjaBulan'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'tmt' => $value['tmt'],
        'nomorBkn' => $value['nomorBkn'],
        'tanggalBkn' => $value['tanggalBkn'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPangkatUpdate' => NULL,
        'keteranganUsulan' => '',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
