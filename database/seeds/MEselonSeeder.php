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
      ['id'=>00,'nama'=>'','idBkn'=>'00'],
      ['id'=>11,'nama'=>'I/a','idBkn'=>'11'],
      ['id'=>12,'nama'=>'I/b','idBkn'=>'12'],
      ['id'=>21,'nama'=>'II/a','idBkn'=>'21'],
      ['id'=>22,'nama'=>'II/b','idBkn'=>'22'],
      ['id'=>31,'nama'=>'III/a','idBkn'=>'31'],
      ['id'=>32,'nama'=>'III/b','idBkn'=>'32'],
      ['id'=>41,'nama'=>'IV/a','idBkn'=>'41'],
      ['id'=>42,'nama'=>'IV/b','idBkn'=>'42'],
      ['id'=>51,'nama'=>'V/a','idBkn'=>'51'],
      ['id'=>52,'nama'=>'V/b','idBkn'=>'52'],
      ['id'=>99,'nama'=>'NON','idBkn'=>'99'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_eselon')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $value['idBkn']
      ]);
    }
  }
}
