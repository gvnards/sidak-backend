<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends JabatanUnitOrganisasiController
{
  // function getRekapJabatan($kodeKomponen = NULL, Request $request) {
  //   $authenticated = $this->isAuth($request)['authenticated'];
  //   $username = $this->isAuth($request)['username'];
  //   if(!$authenticated) return $this->encrypt($username, json_encode([
  //     'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
  //     'status' => $authenticated === true ? 1 : 0
  //   ]));
  //   $this->getJabatan($kodeKomponen, $request);
  //   $this->getUnitOrganisasi($kodeKomponen, $request);
  // }
  function getPegawaiUltah($numberOfMonth, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('v_pegawai')->where([
      ['nip', 'LIKE', "____$numberOfMonth%"]
    ])->get([
      'id',
      'nip',
      'nama',
      'gelarDepan',
      'gelarBelakang'
    ]);
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
