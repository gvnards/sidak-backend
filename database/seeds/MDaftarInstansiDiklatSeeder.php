<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDaftarInstansiDiklatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Pemerintah Kabupaten Situbondo','idBkn'=>'A5EB03E23CD4F6A0E040640A040252AD'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_daftar_instansi_diklat')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $value['idBkn'],
      ]);
    }
  }
}
