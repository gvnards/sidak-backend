<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MStatusPejabatAtasanPenilaiSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id' => 1, 'nama' => 'PNS'],
      ['id' => 2, 'nama' => 'Bukan PNS'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_status_pejabat_atasan_penilai')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
