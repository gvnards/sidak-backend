<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MTingkatPendidikanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Sekolah Dasar'],
      ['id'=>2,'nama'=>'SLTP'],
      ['id'=>3,'nama'=>'SLTP Kejuruan'],
      ['id'=>4,'nama'=>'SLTA'],
      ['id'=>5,'nama'=>'SLTA Kejuruan'],
      ['id'=>6,'nama'=>'SLTA Keguruan'],
      ['id'=>7,'nama'=>'Diploma I'],
      ['id'=>8,'nama'=>'Diploma II'],
      ['id'=>9,'nama'=>'Diploma III/Sarjana Muda'],
      ['id'=>10,'nama'=>'Diploma IV'],
      ['id'=>11,'nama'=>'S-1/Sarjana'],
      ['id'=>12,'nama'=>'S-2'],
      ['id'=>13,'nama'=>'S-3/Doktor'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_tingkat_pendidikan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
