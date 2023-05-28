<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJabatanTugasTambahanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Kepala Sekolah'],
      ['id'=>2,'nama'=>'Kepala Puskesmas'],
      ['id'=>3,'nama'=>'Bendahara'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_jabatan_tugas_tambahan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
