<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppRoleUserPegawaimenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [];
    $id = 1;
    for($i=1; $i<=4; $i++) {
      for($j=1; $j<=9; $j++) {
        if($i!=1 && $i!=4 && $j==1) {}
        else {
          array_push($data, [
            'id' => $id,
            'idAppRoleUser' => $i,
            'idAppPegawaimenu' => $j
          ]);
          $id++;
        }
      }
    }
    foreach ($data as $key => $value) {
      DB::table('m_app_role_user_pegawaimenu')->insert([
        'id' => $value['id'],
        'idAppRoleUser' => $value['idAppRoleUser'],
        'idAppPegawaimenu' => $value['idAppPegawaimenu'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
