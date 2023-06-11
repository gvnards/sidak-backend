<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJenisJabatanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Jabatan Struktural','idBkn'=>'1'],
      ['id'=>2,'nama'=>'Jabatan Fungsional Tertentu','idBkn'=>'2'],
      ['id'=>3,'nama'=>'Jabatan Fungsional Umum','idBkn'=>'4'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_jenis_jabatan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $value['idBkn'],
      ]);
    }
  }
}
