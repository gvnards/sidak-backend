<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDataDiklatSeeder extends Seeder
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
        'idJenisDiklat' => 1,
        'idDaftarDiklat' => 1,
        'namaDiklat' => 'DIKLAT PIM TK. V',
        'lamaDiklat' => 40,
        'tanggalDiklat' => '2023-06-01',
        'idDaftarInstansiDiklat' => 1,
        'institusiPenyelenggara' => 'Pemerintah Kabupaten Situbondo',
        'idDokumen' => NULL,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataDiklatUpdate' => NULL,
        'keteranganUsulan' => '',
      ]
    ];
    foreach ($data as $key => $value) {
      DB::table('m_data_diklat')->insert([
        'id' => $value['id'],
        'idJenisDiklat' => $value['idJenisDiklat'],
        'idDaftarDiklat' => $value['idDaftarDiklat'],
        'namaDiklat' => $value['namaDiklat'],
        'lamaDiklat' => $value['lamaDiklat'],
        'tanggalDiklat' => $value['tanggalDiklat'],
        'idDaftarInstansiDiklat' => $value['idDaftarInstansiDiklat'],
        'institusiPenyelenggara' => $value['institusiPenyelenggara'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataDiklatUpdate' => $value['idDataDiklatUpdate'],
        'keteranganUsulan' => $value['keteranganUsulan'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
