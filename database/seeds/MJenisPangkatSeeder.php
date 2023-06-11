<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJenisPangkatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nama'=>'Golongan dari Pengadaan CPNS/PNS','idBkn'=>'211'],
      ['id'=>2, 'nama'=>'Reguler','idBkn'=>'101'],
      ['id'=>3, 'nama'=>'(Pilihan) Jabatan Struktural','idBkn'=>'201'],
      ['id'=>4, 'nama'=>'(Pilihan) Jabatan Fungsional Tertentu','idBkn'=>'202'],
      ['id'=>5, 'nama'=>'(Pilihan) Penyesuaian Ijazah','idBkn'=>'203'],
      ['id'=>6, 'nama'=>'(Pilihan) Sedang Melaksanakan Tugas Belajar','idBkn'=>'204'],
      ['id'=>7, 'nama'=>'(Pilihan) Selesai Melaksanakan Tugas Belajar','idBkn'=>'205'],
      ['id'=>8, 'nama'=>'(Pilihan) Diperbantukan/Dipekerjakan Instansi Lain','idBkn'=>'206'],
      ['id'=>9, 'nama'=>'(Pilihan) Penemuan Baru','idBkn'=>'207'],
      ['id'=>10, 'nama'=>'(Pilihan) Prestasi Luar Biasa','idBkn'=>'208'],
      ['id'=>11, 'nama'=>'(Pilihan) Pejabat Negara','idBkn'=>'209'],
      ['id'=>12, 'nama'=>'(Pilihan) Selama DPK/DPB','idBkn'=>'210'],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_jenis_pangkat')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $value['idBkn'],
      ]);
    }
  }
}
