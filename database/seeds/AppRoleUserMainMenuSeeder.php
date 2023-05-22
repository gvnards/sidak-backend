<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppRoleUserMainMenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id' => 1,'idAppRoleUser' => 1, 'idAppMainmenu' => 1],
      ['id' => 2,'idAppRoleUser' => 1, 'idAppMainmenu' => 2],
      ['id' => 3,'idAppRoleUser' => 1, 'idAppMainmenu' => 3],
      ['id' => 4,'idAppRoleUser' => 1, 'idAppMainmenu' => 4],
      ['id' => 5,'idAppRoleUser' => 1, 'idAppMainmenu' => 5],
      ['id' => 6,'idAppRoleUser' => 1, 'idAppMainmenu' => 6],
      ['id' => 7,'idAppRoleUser' => 1, 'idAppMainmenu' => 7],
      ['id' => 8,'idAppRoleUser' => 2, 'idAppMainmenu' => 1],
      ['id' => 9,'idAppRoleUser' => 2, 'idAppMainmenu' => 2],
      ['id' => 10,'idAppRoleUser' => 2, 'idAppMainmenu' => 5],
      ['id' => 11,'idAppRoleUser' => 2, 'idAppMainmenu' => 6],
      ['id' => 12,'idAppRoleUser' => 2, 'idAppMainmenu' => 7],
      ['id' => 13,'idAppRoleUser' => 3, 'idAppMainmenu' => 1],
      ['id' => 14,'idAppRoleUser' => 3, 'idAppMainmenu' => 2],
      ['id' => 15,'idAppRoleUser' => 3, 'idAppMainmenu' => 5],
      ['id' => 16,'idAppRoleUser' => 3, 'idAppMainmenu' => 6],
      ['id' => 17,'idAppRoleUser' => 3, 'idAppMainmenu' => 7],
      ['id' => 18,'idAppRoleUser' => 4, 'idAppMainmenu' => 1],
      ['id' => 19,'idAppRoleUser' => 4, 'idAppMainmenu' => 2],
      ['id' => 20,'idAppRoleUser' => 4, 'idAppMainmenu' => 5],
      ['id' => 21,'idAppRoleUser' => 4, 'idAppMainmenu' => 6],
      ['id' => 22,'idAppRoleUser' => 4, 'idAppMainmenu' => 7],
      ['id' => 23,'idAppRoleUser' => 1, 'idAppMainmenu' => 8],
    ];
    foreach($data as $key => $value) {
      DB::table('m_app_role_user_mainmenu')->insert([
        'id' => $value['id'],
        'idAppRoleUser' => $value['idAppRoleUser'],
        'idAppMainmenu' => $value['idAppMainmenu'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
