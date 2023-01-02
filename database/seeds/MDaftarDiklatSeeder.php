<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDaftarDiklatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'SEPADA','idJenisDiklat'=>1],
      ['id'=>2,'nama'=>'SEPALA/ADUM/DIKLAT PIM TK. IV','idJenisDiklat'=>1],
      ['id'=>3,'nama'=>'SEPADYA/SPAMA/DIKLAT PIM TK. III','idJenisDiklat'=>1],
      ['id'=>4,'nama'=>'SPAMEN/SESPA/SESPANAS/DIKLAT PIM TK. II','idJenisDiklat'=>1],
      ['id'=>5,'nama'=>'SEPATI/DIKLAT PIM TK. I','idJenisDiklat'=>1],
      ['id'=>6,'nama'=>'Diklat Fungsional a,b,c,d...','idJenisDiklat'=>2],
      ['id'=>7,'nama'=>'Diklat Teknis a,b,c,d...','idJenisDiklat'=>3],
      ['id'=>8,'nama'=>'Seminar/Workshop a,b,c,d...','idJenisDiklat'=>4],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_daftar_diklat')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'idJenisDiklat' => $value['idJenisDiklat'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
