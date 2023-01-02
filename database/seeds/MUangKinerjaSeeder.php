<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MUangKinerjaSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id'=>1, 'nominal'=>0],
      ['id'=>2, 'nominal'=>661927],
      ['id'=>3, 'nominal'=>775872],
      ['id'=>4, 'nominal'=>864316],
      ['id'=>5, 'nominal'=>956815],
      ['id'=>6, 'nominal'=>1012042],
      ['id'=>7, 'nominal'=>1213524],
      ['id'=>8, 'nominal'=>1396480],
      ['id'=>9, 'nominal'=>1583856],
      ['id'=>10, 'nominal'=>1970609],
      ['id'=>11, 'nominal'=>2116364],
      ['id'=>12, 'nominal'=>2265358],
      ['id'=>13, 'nominal'=>2435434],
      ['id'=>14, 'nominal'=>2604320],
      ['id'=>15, 'nominal'=>2762215],
      ['id'=>16, 'nominal'=>2991833],
      ['id'=>17, 'nominal'=>3146838],
      ['id'=>18, 'nominal'=>3368562],
      ['id'=>19, 'nominal'=>3456848],
      ['id'=>20, 'nominal'=>3869792],
      ['id'=>21, 'nominal'=>3950742],
      ['id'=>22, 'nominal'=>4070283],
      ['id'=>23, 'nominal'=>4471267],
      ['id'=>24, 'nominal'=>4571513],
      ['id'=>25, 'nominal'=>4671759],
      ['id'=>26, 'nominal'=>6370118],
      ['id'=>27, 'nominal'=>6594839],
      ['id'=>28, 'nominal'=>7068550],
      ['id'=>29, 'nominal'=>7177906],
      ['id'=>30, 'nominal'=>7347922],
      ['id'=>31, 'nominal'=>7945780],
      ['id'=>32, 'nominal'=>16579753],
    ];

    foreach ($data as $key => $value) {
      DB::table('m_uang_kinerja')->insert([
        'id' => $value['id'],
        'nominal' => $value['nominal'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
  }
}
