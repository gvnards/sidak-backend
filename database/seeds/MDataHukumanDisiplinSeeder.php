<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDataHukumanDisiplinSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [];

    foreach ($data as $key => $value) {
      DB::table('m_data_hukuman_disiplin')->insert([
        'id' => $value['id'],
        'idJenisHukumanDisiplin' => $value['idJenisHukumanDisiplin'],
        'idDaftarHukumanDisiplin' => $value['idDaftarHukumanDisiplin'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'tmtAwal' => $value['tmtAwal'],
        'masaHukuman' => $value['masaHukuman'],
        'tmtAkhir' => $value['tmtAkhir'],
        'idDaftarDasarHukumHukdis' => $value['idDaftarDasarHukumHukdis'],
        'idDaftarAlasanHukdis' => $value['idDaftarAlasanHukdis'],
        'keteranganAlasanHukdis' => $value['keteranganAlasanHukdis'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'keteranganUsulan' => $value['keteranganUsulan'],
        'idDataHukumanDisiplinUpdate' => $value['idDataHukumanDisiplinUpdate'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
