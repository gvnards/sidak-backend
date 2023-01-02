<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MDataAnakSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      [
        'id' => 1,
        'nama' => 'Alhamdulillah Cowok',
        'tempatLahir' => 'Semarang',
        'tanggalLahir' => '2023-12-12',
        'nomorDokumen' => 'XXX/XXX/XXX',
        'tanggalDokumen' => '2023-12-13',
        'idOrangTua' => 3,
        'idStatusAnak' => 1,
        'idDokumen' => 1,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
      ],
      [
        'id' => 2,
        'nama' => 'Alhamdulillah Cewek',
        'tempatLahir' => 'Semarang',
        'tanggalLahir' => '2025-12-12',
        'nomorDokumen' => 'XXX/XXX/XXX',
        'tanggalDokumen' => '2025-12-13',
        'idOrangTua' => 3,
        'idStatusAnak' => 1,
        'idDokumen' => 2,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 3,
        'idUsulanHasil' => 1,
      ],
      [
        'id' => 3,
        'nama' => 'Alhamdulillah Cowok Lagi',
        'tempatLahir' => 'Semarang',
        'tanggalLahir' => '2027-12-12',
        'nomorDokumen' => 'XXX/XXX/XXX',
        'tanggalDokumen' => '2027-12-13',
        'idOrangTua' => 3,
        'idStatusAnak' => 1,
        'idDokumen' => 1,
        'idPegawai' => 5324,
        'idUsulan' => 1,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ],
    ];
    foreach ($data as $key => $value) {
      DB::table('m_data_anak')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'tempatLahir' => $value['tempatLahir'],
        'tanggalLahir' => $value['tanggalLahir'],
        'nomorDokumen' => $value['nomorDokumen'],
        'tanggalDokumen' => $value['tanggalDokumen'],
        'idOrangTua' => $value['idOrangTua'],
        'idStatusAnak' => $value['idStatusAnak'],
        'idDokumen' => $value['idDokumen'],
        'idPegawai' => $value['idPegawai'],
        'idUsulan' => $value['idUsulan'],
        'idUsulanStatus' => $value['idUsulanStatus'],
        'idUsulanHasil' => $value['idUsulanHasil'],
        'idDataAnakUpdate' => NULL,
        'keteranganUsulan' => '',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
