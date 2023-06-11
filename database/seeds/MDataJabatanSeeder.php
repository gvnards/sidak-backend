<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MDataJabatanSeeder extends Seeder
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
      DB::table('m_data_jabatan')->insert([
        'id' => NULL,
        'idJabatan' => $value['idJabatan'],
        'isPltPlh' => 0,
        'tmt' => '2023-02-01',
        'spmt' => '2023-02-01',
        'tanggalDokumen' => '2023-02-01',
        'nomorDokumen' => '',
        'idDokumen' => NULL,
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataJabatanUpdate' => NULL,
        'keteranganUsulan' => '',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
