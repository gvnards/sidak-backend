<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppRoleUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id' => 1,'nama' => 'super-admin'],
      ['id' => 2,'nama' => 'admin-bkpsdm'],
      ['id' => 3,'nama' => 'admin-opd'],
      ['id' => 4,'nama' => 'pegawai'],
    ];
    foreach($data as $key => $value) {
      DB::table('m_app_role_user')->insert([
        'id' => $value['id'],
        'nama' => $value['nama'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
