<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MStatusAnakSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nama'=>'Anak Kandung'],
      ['id'=>2, 'nama'=>'Anak Tiri'],
      ['id'=>3, 'nama'=>'Anak Angkat'],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_status_anak')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
