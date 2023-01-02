<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MUsulanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nama'=>'Penambahan'],
      ['id'=>2, 'nama'=>'Pembaharuan'],
      ['id'=>3, 'nama'=>'Penghapusan'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_usulan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
