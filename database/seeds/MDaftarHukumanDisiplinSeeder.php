<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MDaftarHukumanDisiplinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $data = [
        ['id'=>1,'nama'=>'PEMBEBASAN DARI JABATAN','idJenisHukumanDisiplin'=>3],
        ['id'=>2,'nama'=>'PEMBERHENTIAN DENGAN HORMAT TIDAK ATAS PERMINTAAN SENDIRI','idJenisHukumanDisiplin'=>3],
        ['id'=>3,'nama'=>'PEMBERHENTIAN TIDAK DENGAN HORMAT SEBAGAI PNS','idJenisHukumanDisiplin'=>3],
        ['id'=>4,'nama'=>'PENGAKTIFAN HUKUMAN DISIPLIN','idJenisHukumanDisiplin'=>3],
        ['id'=>5,'nama'=>'PEMINDAHAN DLM RANGKA PENURUNAN JABATAN 1 TINGKAT','idJenisHukumanDisiplin'=>3],
        ['id'=>6,'nama'=>'PENURUNAN PANGKAT 1 TINGKAT 3 THN','idJenisHukumanDisiplin'=>3],
        ['id'=>7,'nama'=>'PENURUNAN JABATAN 1 TINGKAT 12 BLN','idJenisHukumanDisiplin'=>3],
        ['id'=>8,'nama'=>'PENURUNAN DARI JABATAN SETINGKAT LEBIH RENDAH SELAMA 12 BLN','idJenisHukumanDisiplin'=>3],
        ['id'=>9,'nama'=>'PEMBEBASAN DARI JABATAN MENJADI PELAKSANA SELAMA 12 BLN','idJenisHukumanDisiplin'=>3],
        ['id'=>10,'nama'=>'TEGURAN LISAN','idJenisHukumanDisiplin'=>1],
        ['id'=>11,'nama'=>'TEGURAN TERTULIS','idJenisHukumanDisiplin'=>1],
        ['id'=>12,'nama'=>'PERNYATAAN TIDAK PUAS SECARA TERTULIS','idJenisHukumanDisiplin'=>1],
        ['id'=>13,'nama'=>'SANKSI MORAL TERBUKA','idJenisHukumanDisiplin'=>1],
        ['id'=>14,'nama'=>'SANKSI MORAL TERTUTUP','idJenisHukumanDisiplin'=>1],
        ['id'=>15,'nama'=>'PENUNDAAN KGB SELAMA 1 THN','idJenisHukumanDisiplin'=>2],
        ['id'=>16,'nama'=>'PENURUNAN GAJI MAX 1 TH','idJenisHukumanDisiplin'=>2],
        ['id'=>17,'nama'=>'PENUNDAAN GAJI MAX 1 THN','idJenisHukumanDisiplin'=>2],
        ['id'=>18,'nama'=>'PENUNDAAN KP SELAMA 1 THN','idJenisHukumanDisiplin'=>2],
        ['id'=>19,'nama'=>'PENURUNAN PANGKAT 1 TINGKAT 1 THN','idJenisHukumanDisiplin'=>2],
        ['id'=>20,'nama'=>'PEMOTONGAN TUNKIN 25% 6 BLN','idJenisHukumanDisiplin'=>2],
        ['id'=>21,'nama'=>'PEMOTONGAN TUNKIN 25% 9 BLN','idJenisHukumanDisiplin'=>2],
        ['id'=>22,'nama'=>'PEMOTONGAN TUNKIN 25% 12 BLN','idJenisHukumanDisiplin'=>2],
      ];

      foreach ($data as $key => $value) {
        DB::table('m_daftar_hukuman_disiplin')->insert([
          'id' => $value['id'],
          'nama' => $value['nama'],
          'idJenisHukumanDisiplin' => $value['idJenisHukumanDisiplin'],
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
      }
    }
}
