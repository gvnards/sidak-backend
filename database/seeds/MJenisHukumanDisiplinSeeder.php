<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJenisHukumanDisiplinSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Ringan'],
      ['id'=>2,'nama'=>'Sedang'],
      ['id'=>3,'nama'=>'Berat'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_jenis_hukuman_disiplin')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
