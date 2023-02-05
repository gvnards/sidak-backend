<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AppPegawaimenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id' => 1,'nama' => 'Data Pribadi', 'illustration' => 'IllustrationDataPribadi'],
      ['id' => 2,'nama' => 'Data Keluarga', 'illustration' => 'IllustrationDataKeluarga'],
      ['id' => 3,'nama' => 'Data Pendidikan', 'illustration' => 'IllustrationDataPendidikan'],
      ['id' => 4,'nama' => 'Data CPNS/PNS', 'illustration' => 'IllustrationDataCpnsPns'],
      ['id' => 5,'nama' => 'Data Pangkat/Golongan', 'illustration' => 'IllustrationDataPangkatGolongan'],
      ['id' => 6,'nama' => 'Data Jabatan/Unit Kerja', 'illustration' => 'IllustrationDataJabatanUnitKerja'],
      ['id' => 7,'nama' => 'Data SKP', 'illustration' => 'IllustrationDataSkp'],
      ['id' => 8,'nama' => 'Data Diklat/Kursus', 'illustration' => 'IllustrationDataDiklatKursus'],
      ['id' => 9,'nama' => 'Data Hukuman Disiplin', 'illustration' => 'IllustrationDataHukumanDisiplin'],
      ['id' => 10,'nama' => 'Data Status Kepegawaian', 'illustration' => 'IllustrationDataStatusKepegawaian'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_app_pegawaimenu')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'illustration' => $value['illustration'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
