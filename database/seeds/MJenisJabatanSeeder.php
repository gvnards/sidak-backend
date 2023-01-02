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
      ['id'=>1,'nama'=>'Jabatan Struktural'],
      ['id'=>2,'nama'=>'Jabatan Fungsional Tertentu'],
      ['id'=>3,'nama'=>'Jabatan Fungsional Umum'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_jenis_jabatan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
