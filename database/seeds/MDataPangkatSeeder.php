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
    $data = [
      [
        'id' => 1,
        'idJenisPangkat' => 1,
        'idDaftarPangkat' => 31,
        'masaKerjaTahun' => 0,
        'masaKerjaBulan' => 0,
        'nomorDokumen' => '813/344/431.303.2/2020',
        'tanggalDokumen' => '2020-02-28',
        'tmt' => '2020-03-01',
        'nomorBkn' => 'AG-23512000113',
        'tanggalBkn' => '2019-02-14',
        'idDokumen' => NULL,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPangkatUpdate' => NULL,
        'keteranganUsulan' => '',
      ],
      [
        'id' => 2,
        'idJenisPangkat' => 2,
        'idDaftarPangkat' => 32,
        'masaKerjaTahun' => 4,
        'masaKerjaBulan' => 0,
        'nomorDokumen' => '813/344/431.303.2/2023',
        'tanggalDokumen' => '2023-02-28',
        'tmt' => '2023-03-01',
        'nomorBkn' => 'AG-23451200520',
        'tanggalBkn' => '2023-02-14',
        'idDokumen' => 2,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPangkatUpdate' => NULL,
        'keteranganUsulan' => '',
      ],
    ];

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
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataPangkatUpdate' => $value['idDataPangkatUpdate'],
        'keteranganUsulan' => $value['keteranganUsulan'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
