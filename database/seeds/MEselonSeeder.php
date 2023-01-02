<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MEselonSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>11,'nama'=>'I/a'],
      ['id'=>12,'nama'=>'I/b'],
      ['id'=>21,'nama'=>'II/a'],
      ['id'=>22,'nama'=>'II/b'],
      ['id'=>31,'nama'=>'III/a'],
      ['id'=>32,'nama'=>'III/b'],
      ['id'=>41,'nama'=>'IV/a'],
      ['id'=>42,'nama'=>'IV/b'],
      ['id'=>51,'nama'=>'V/a'],
      ['id'=>52,'nama'=>'V/b'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_eselon')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
