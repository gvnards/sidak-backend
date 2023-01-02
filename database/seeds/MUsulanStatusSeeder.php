<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MUsulanStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Belum Diproses'],
      ['id'=>2,'nama'=>'Sedang Diproses'],
      ['id'=>3,'nama'=>'Sudah Diproses'],
      ['id'=>4,'nama'=>'Diproses oleh BKPSDM'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_usulan_status')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
