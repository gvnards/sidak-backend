<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppMainmenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1,'nama'=>'Dashboard','icon'=>'fa-solid fa-rocket','order'=>1],
      ['id'=>2,'nama'=>'Pegawai','icon'=>'fa-solid fa-user-tie','order'=>2],
      ['id'=>3,'nama'=>'Jabatan','icon'=>'fa-solid fa-briefcase','order'=>3],
      ['id'=>4,'nama'=>'Unit Organisasi','icon'=>'fa-solid fa-building','order'=>4],
      ['id'=>5,'nama'=>'Usulan','icon'=>'fa-solid fa-envelope-open-text','order'=>5],
      ['id'=>6,'nama'=>'Ubah Password','icon'=>'fa-solid fa-key','order'=>7],
      ['id'=>7,'nama'=>'Logout','icon'=>'fa-solid fa-right-from-bracket','order'=>8],
      ['id'=>8,'nama'=>'Akun Pengguna','icon'=>'fa-solid fa-user-shield','order'=>6],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_app_mainmenu')->insert(
        [
          'id' => $value['id'],
          'nama' => $value['nama'],
          'icon' => $value['icon'],
          'order' => $value['order'],
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]
      );
    }
  }
}
