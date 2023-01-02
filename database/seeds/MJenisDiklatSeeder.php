<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJenisDiklatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Diklat Struktural'],
      ['id'=>2,'nama'=>'Diklat Fungsional'],
      ['id'=>3,'nama'=>'Diklat Teknis'],
      ['id'=>4,'nama'=>'Seminar/Workshop/Magang/Sejenisnya'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_jenis_diklat')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
