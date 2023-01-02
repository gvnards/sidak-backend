<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MKelasJabatanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nama'=>'0', 'idUangKinerja'=>1],
      ['id'=>2, 'nama'=>'1', 'idUangKinerja'=>2],
      ['id'=>3, 'nama'=>'2', 'idUangKinerja'=>3],
      ['id'=>4, 'nama'=>'3', 'idUangKinerja'=>4],
      ['id'=>5, 'nama'=>'4', 'idUangKinerja'=>5],
      ['id'=>6, 'nama'=>'5', 'idUangKinerja'=>6],
      ['id'=>7, 'nama'=>'6', 'idUangKinerja'=>7],
      ['id'=>8, 'nama'=>'7', 'idUangKinerja'=>8],
      ['id'=>9, 'nama'=>'8', 'idUangKinerja'=>9],
      ['id'=>10, 'nama'=>'9', 'idUangKinerja'=>10],
      ['id'=>11, 'nama'=>'6.A', 'idUangKinerja'=>11],
      ['id'=>12, 'nama'=>'10', 'idUangKinerja'=>12],
      ['id'=>13, 'nama'=>'7.A', 'idUangKinerja'=>13],
      ['id'=>14, 'nama'=>'11', 'idUangKinerja'=>14],
      ['id'=>15, 'nama'=>'8.A', 'idUangKinerja'=>15],
      ['id'=>16, 'nama'=>'11.C', 'idUangKinerja'=>16],
      ['id'=>17, 'nama'=>'11.B', 'idUangKinerja'=>17],
      ['id'=>18, 'nama'=>'12', 'idUangKinerja'=>18],
      ['id'=>19, 'nama'=>'11.A', 'idUangKinerja'=>19],
      ['id'=>20, 'nama'=>'12.E', 'idUangKinerja'=>20],
      ['id'=>21, 'nama'=>'10.A', 'idUangKinerja'=>21],
      ['id'=>22, 'nama'=>'12.D', 'idUangKinerja'=>22],
      ['id'=>23, 'nama'=>'12.C', 'idUangKinerja'=>23],
      ['id'=>24, 'nama'=>'12.F', 'idUangKinerja'=>24],
      ['id'=>25, 'nama'=>'12.B', 'idUangKinerja'=>25],
      ['id'=>26, 'nama'=>'14', 'idUangKinerja'=>26],
      ['id'=>27, 'nama'=>'13.A', 'idUangKinerja'=>27],
      ['id'=>28, 'nama'=>'14.C', 'idUangKinerja'=>28],
      ['id'=>29, 'nama'=>'12.A', 'idUangKinerja'=>29],
      ['id'=>30, 'nama'=>'14.A', 'idUangKinerja'=>30],
      ['id'=>31, 'nama'=>'14.B', 'idUangKinerja'=>31],
      ['id'=>32, 'nama'=>'15', 'idUangKinerja'=>32],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_kelas_jabatan')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'idUangKinerja' => $value['idUangKinerja'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
