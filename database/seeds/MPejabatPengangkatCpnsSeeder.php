<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MPejabatPengangkatCpnsSeeder extends Seeder
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
        'nama' => 'Bupati'
      ],
      [
        'id' => 2,
        'nama' => 'Sekretaris Daerah'
      ],
      [
        'id' => 3,
        'nama' => 'Kepala Badan Kepegawaian dan Pengembangan Sumber Daya Manusia'
      ]
    ];
    foreach ($data as $key => $value) {
      DB::table('m_pejabat_pengangkat_cpns')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
