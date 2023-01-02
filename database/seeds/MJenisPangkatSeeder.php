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
      ['id'=>1, 'nama'=>'Golongan dari Pengadaan CPNS/PNS'],
      ['id'=>2, 'nama'=>'Reguler'],
      ['id'=>3, 'nama'=>'(Pilihan) Jabatan Struktural'],
      ['id'=>4, 'nama'=>'(Pilihan) Jabatan Fungsional Tertentu'],
      ['id'=>5, 'nama'=>'(Pilihan) Penyesuaian Ijazah'],
      ['id'=>6, 'nama'=>'(Pilihan) Sedang Melaksanakan Tugas Belajar'],
      ['id'=>7, 'nama'=>'(Pilihan) Selesai Melaksanakan Tugas Belajar'],
      ['id'=>8, 'nama'=>'(Pilihan) Diperbantukan/Dipekerjakan Instansi Lain'],
      ['id'=>9, 'nama'=>'(Pilihan) Penemuan Baru'],
      ['id'=>10, 'nama'=>'(Pilihan) Prestasi Luar Biasa'],
      ['id'=>11, 'nama'=>'(Pilihan) Pejabat Negara'],
      ['id'=>12, 'nama'=>'(Pilihan) Selama DPK/DPB'],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_jenis_pangkat')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
