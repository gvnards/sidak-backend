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
      ['id'=>1,'golongan'=>'I/a','pangkat'=>'Juru Muda','idBkn'=>11],
      ['id'=>2,'golongan'=>'I/b','pangkat'=>'Juru Muda Tingkat I','idBkn'=>12],
      ['id'=>3,'golongan'=>'I/c','pangkat'=>'Juru','idBkn'=>13],
      ['id'=>4,'golongan'=>'I/d','pangkat'=>'Juru TIngkat I','idBkn'=>14],
      ['id'=>5,'golongan'=>'II/a','pangkat'=>'Pengatur Muda','idBkn'=>21],
      ['id'=>6,'golongan'=>'II/b','pangkat'=>'Pengatur Muda Tingkat I','idBkn'=>22],
      ['id'=>7,'golongan'=>'II/c','pangkat'=>'Pengatur','idBkn'=>23],
      ['id'=>8,'golongan'=>'II/d','pangkat'=>'Pengatur Tingkat I','idBkn'=>24],
      ['id'=>9,'golongan'=>'III/a','pangkat'=>'Penata Muda','idBkn'=>31],
      ['id'=>10,'golongan'=>'III/b','pangkat'=>'Penata Muda Tingkat I','idBkn'=>32],
      ['id'=>11,'golongan'=>'III/c','pangkat'=>'Penata','idBkn'=>33],
      ['id'=>12,'golongan'=>'III/d','pangkat'=>'Penata Tingkat I','idBkn'=>34],
      ['id'=>13,'golongan'=>'IV/a','pangkat'=>'Pembina','idBkn'=>41],
      ['id'=>14,'golongan'=>'IV/b','pangkat'=>'Pembina Tingkat I','idBkn'=>42],
      ['id'=>15,'golongan'=>'IV/c','pangkat'=>'Pembina Utama Muda','idBkn'=>43],
      ['id'=>16,'golongan'=>'IV/d','pangkat'=>'Pembina Utama Madya','idBkn'=>44],
      ['id'=>17,'golongan'=>'IV/e','pangkat'=>'Pembina Utama','idBkn'=>45],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_daftar_pangkat')->insert([
        'id' => $value['id'],
        'golongan' => $value['golongan'],
        'pangkat' => $value['pangkat'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $value['idBkn']
      ]);
    }
  }
}
