<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MDaftarStatusKepegawaianSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'CPNS Umum'],
      ['id'=>2,'nama'=>'CPNS Sertifikasi'],
      ['id'=>3,'nama'=>'CPNS BLUD'],
      ['id'=>4,'nama'=>'PNS Umum'],
      ['id'=>5,'nama'=>'PNS Sertifikasi'],
      ['id'=>6,'nama'=>'PNS BLUD'],
      ['id'=>7,'nama'=>'PNS Kementerian/Pusat'],
      ['id'=>8,'nama'=>'Pensiun'],
      ['id'=>9,'nama'=>'Meninggal'],
      ['id'=>10,'nama'=>'Pindah Keluar Pemerintah Situbondo'],
      ['id'=>11,'nama'=>'Pecat'],
      ['id'=>12,'nama'=>'Berhenti'],
      ['id'=>13,'nama'=>'Kepala Desa'],
      ['id'=>14,'nama'=>'Non ASN'],
      ['id'=>15,'nama'=>'PPPK Teknis'],
      ['id'=>16,'nama'=>'PPPK Kesehatan'],
      ['id'=>17,'nama'=>'PPPK Guru'],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_daftar_status_kepegawaian')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
