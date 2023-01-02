<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MJenisPendidikanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Pendidikan saat sebelum sebagai ASN'],
      ['id'=>2,'nama'=>'Pendidikan saat pengangkatan sebagai ASN'],
      ['id'=>3,'nama'=>'Pendidikan saat sebagai ASN'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_jenis_pendidikan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
