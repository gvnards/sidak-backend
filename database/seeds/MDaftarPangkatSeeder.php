<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDaftarPangkatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>11,'golongan'=>'I/a','pangkat'=>'Juru Muda'],
      ['id'=>12,'golongan'=>'I/b','pangkat'=>'Juru Muda Tingkat I'],
      ['id'=>13,'golongan'=>'I/c','pangkat'=>'Juru'],
      ['id'=>14,'golongan'=>'I/d','pangkat'=>'Juru TIngkat I'],
      ['id'=>21,'golongan'=>'II/a','pangkat'=>'Pengatur Muda'],
      ['id'=>22,'golongan'=>'II/b','pangkat'=>'Pengatur Muda Tingkat I'],
      ['id'=>23,'golongan'=>'II/c','pangkat'=>'Pengatur'],
      ['id'=>24,'golongan'=>'II/d','pangkat'=>'Pengatur Tingkat I'],
      ['id'=>31,'golongan'=>'III/a','pangkat'=>'Penata Muda'],
      ['id'=>32,'golongan'=>'III/b','pangkat'=>'Penata Muda Tingkat I'],
      ['id'=>33,'golongan'=>'III/c','pangkat'=>'Penata'],
      ['id'=>34,'golongan'=>'III/d','pangkat'=>'Penata Tingkat I'],
      ['id'=>41,'golongan'=>'IV/a','pangkat'=>'Pembina'],
      ['id'=>42,'golongan'=>'IV/b','pangkat'=>'Pembina Tingkat I'],
      ['id'=>43,'golongan'=>'IV/c','pangkat'=>'Pembina Utama Muda'],
      ['id'=>44,'golongan'=>'IV/d','pangkat'=>'Pembina Utama Madya'],
      ['id'=>45,'golongan'=>'IV/e','pangkat'=>'Pembina Utama'],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_daftar_pangkat')->insert([
        'id' => $value['id'],
        'golongan' => $value['golongan'],
        'pangkat' => $value['pangkat'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
