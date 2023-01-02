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
    $data = [
      [
        'id' => 1,
        'idJenisPendidikan' => 1,
        'idTingkatPendidikan' => 1,
        'idDaftarPendidikan' => 3,
        'namaSekolah' => 'SD Negeri Sendangmulyo 03-04',
        'gelarDepan' => '',
        'gelarBelakang' => '',
        'tanggalLulus' => '2008-07-01',
        'tahunLulus' => '2008',
        'nomorDokumen' => 'XXXX/XXXX',
        'tanggalDokumen' => '2008-07-10',
        'idDokumen' => NULL,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPendidikanUpdate' => NULL,
        'keteranganUsulan' => '',
      ],
      [
        'id' => 2,
        'idJenisPendidikan' => 2,
        'idTingkatPendidikan' => 11,
        'idDaftarPendidikan' => 9260,
        'namaSekolah' => 'Universitas Dian Nuswantoro',
        'gelarDepan' => '',
        'gelarBelakang' => 'S.Kom.',
        'tanggalLulus' => '2018-03-01',
        'tahunLulus' => '2018',
        'nomorDokumen' => 'XXXX/XXXX',
        'tanggalDokumen' => '2018-03-10',
        'idDokumen' => 2,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPendidikanUpdate' => NULL,
        'keteranganUsulan' => '',
      ]
    ];
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
