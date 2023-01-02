<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDokumenKategoriSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nama'=>'DOK_AKTA_PERKAWINAN_', 'formatNama'=>'DOK_AKTA_PERKAWINAN_{NIP}', 'keterangan'=>'Dokumen Akta Perkawinan', 'ukuran'=>0.25],
      ['id'=>2, 'nama'=>'DOK_AKTA_ANAK_', 'formatNama'=>'DOK_AKTA_ANAK_{NIP}', 'keterangan'=>'Dokumen Akta Kelahiran Anak', 'ukuran'=>0.25],
      ['id'=>3, 'nama'=>'DOK_IJAZAH_', 'formatNama'=>'DOK_IJAZAH_{NIP}', 'keterangan'=>'Dokumen Ijazah Pendidikan', 'ukuran'=>0.25],
      ['id'=>4, 'nama'=>'DOK_SK_PANGKAT_', 'formatNama'=>'DOK_SK_PANGKAT_{NIP}', 'keterangan'=>'Dokumen SK Kenaikan Pangkat', 'ukuran'=>0.25],
      ['id'=>5, 'nama'=>'DOK_SERTIFIKAT_DIKLAT_', 'formatNama'=>'DOK_SERTIFIKAT_DIKLAT_{NIP}', 'keterangan'=>'Dokumen Sertifikat Diklat/Kursus', 'ukuran'=>0.25],
      ['id'=>6, 'nama'=>'DOK_HUKUMAN_DISIPLIN_', 'formatNama'=>'DOK_HUKUMAN_DISIPLIN_{NIP}', 'keterangan'=>'Dokumen Hukuman Disiplin', 'ukuran'=>0.25],
      ['id'=>7, 'nama'=>'DOK_SK_CPNS_', 'formatNama'=>'DOK_SK_CPNS_{NIP}', 'keterangan'=>'Dokumen SK CPNS', 'ukuran'=>0.5],
      ['id'=>8, 'nama'=>'DOK_SK_PNS_', 'formatNama'=>'DOK_SK_PNS_{NIP}', 'keterangan'=>'Dokumen SK PNS', 'ukuran'=>0.5],
      ['id'=>9, 'nama'=>'DOK_SKP_', 'formatNama'=>'DOK_SKP_{NIP}', 'keterangan'=>'Dokumen SKP', 'ukuran'=>2],
      ['id'=>10, 'nama'=>'DOK_SK_JABATAN_', 'formatNama'=>'DOK_SK_JABATAN_{NIP}', 'keterangan'=>'Dokumen SK Jabatan', 'ukuran'=>0.5],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_dokumen_kategori')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'formatNama' => $value['formatNama'],
        'keterangan' => $value['keterangan'],
        'ukuran' => $value['ukuran'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
