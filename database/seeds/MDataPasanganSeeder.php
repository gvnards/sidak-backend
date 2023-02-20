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
    $data = [['id' => 3,
    'nama' => 'Ivana Rantansari',
    'tempatLahir' => 'Semarang',
    'tanggalLahir' => '1996-03-07',
    'tanggalStatusPerkawinan' => '2021-07-10',
    'nomorDokumen' => 'nanti',
    'tanggalDokumen' => '2021-07-10',
    'idStatusPerkawinan' => 1,
    'idDokumen' => NULL,
    'idPegawai' => 5324,
    'idUsulan' => 1,
    'idUsulanStatus' => 4,
    'idUsulanHasil' => 1,
    'keteranganUsulan' => '',
  'idDataPasanganUpdate' => NULL],
    ['id' => 2,
    'nama' => 'I Rantansari',
    'tempatLahir' => 'Semarang',
    'tanggalLahir' => '1996-03-07',
    'tanggalStatusPerkawinan' => '2021-07-10',
    'nomorDokumen' => 'nanti',
    'tanggalDokumen' => '2021-07-10',
    'idStatusPerkawinan' => 1,
    'idDokumen' => NULL,
    'idPegawai' => 5324,
    'idUsulan' => 1,
    'idUsulanStatus' => 1,
    'idUsulanHasil' => 3,
    'keteranganUsulan' => '',
  'idDataPasanganUpdate' => NULL],
    ['id' => 6,
    'nama' => 'I R',
    'tempatLahir' => 'Semarang',
    'tanggalLahir' => '1996-03-07',
    'tanggalStatusPerkawinan' => '2021-07-10',
    'nomorDokumen' => 'nanti',
    'tanggalDokumen' => '2021-07-10',
    'idStatusPerkawinan' => 1,
    'idDokumen' => NULL,
    'idPegawai' => 5324,
    'idUsulan' => 1,
    'idUsulanStatus' => 3,
    'idUsulanHasil' => 1,
    'keteranganUsulan' => '',
    'idDataPasanganUpdate' => NULL]];
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
