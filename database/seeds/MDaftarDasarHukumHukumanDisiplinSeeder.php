<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDaftarDasarHukumHukumanDisiplinSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'PP 11 Tahun 2017'],
      ['id'=>2,'nama'=>'UU 5 Tahun 2014'],
      ['id'=>3,'nama'=>'PP 53 Tahun 2010'],
      ['id'=>4,'nama'=>'PP 45 Tahun 1990'],
      ['id'=>5,'nama'=>'PP 10 Tahun 1983'],
      ['id'=>6,'nama'=>'PP 30 Tahun 1980'],
      ['id'=>7,'nama'=>'PP 32 Tahun 1979'],
      ['id'=>8,'nama'=>'PP 94 Tahun 2021'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_daftar_dasar_hukum_hukuman_disiplin')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
