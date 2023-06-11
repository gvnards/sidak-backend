<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MDataAnakSeeder extends Seeder
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
      DB::table('m_data_anak')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'tempatLahir' => $value['tempatLahir'],
        'tanggalLahir' => $value['tanggalLahir'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'idOrangTua' => $value['idOrangTua'],
        'idStatusAnak' => $value['idStatusAnak'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataAnakUpdate' => NULL,
        'keteranganUsulan' => '',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
