<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListPegawaiController extends Controller
{
  public function getListPegawai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->header('Authorization'));
    $message = json_decode($message, true);
    $idAppRoleUser = $message['idAppRoleUser'];
    $kodeKomponenAdmin = '';
    $kdKom = DB::table('m_admin')->where('username', '=', $username)->get();
    foreach(json_decode($kdKom, true) as $key => $value) {
      $kodeKomponenAdmin = $kodeKomponenAdmin.$value['unitOrganisasi'].'%';
    }
    if ($idAppRoleUser === 1) {
      $data = DB::table('v_short_brief')->groupBy('id')->get([
        'id as id',
        'nama as nama',
        'nip as nip',
        'jabatan as jabatan',
        'unitOrganisasi as unitOrganisasi',
        'golongan as golongan',
        'pangkat as pangkat'
      ]);
    } else {
      $data = DB::table('v_short_brief')->where('kodeKomponen', 'LIKE', $kodeKomponenAdmin)->groupBy('id')->get([
        'id as id',
        'nama as nama',
        'nip as nip',
        'jabatan as jabatan',
        'unitOrganisasi as unitOrganisasi',
        'golongan as golongan',
        'pangkat as pangkat'
      ]);
    }
    $namaUnitOrganisasi = json_decode(DB::table('m_admin')->join('m_unit_organisasi', 'm_admin.unitOrganisasi', '=', 'm_unit_organisasi.kodeKomponen')->where([
      ['m_admin.username', '=', $username]
    ])->get([
      'm_unit_organisasi.nama as unitOrganisasi'
    ]), true);
    $callback = [
      'message' => [
        'pegawai' => $data,
        'unitOrganisasi' => $namaUnitOrganisasi[0]['unitOrganisasi']
      ],
      'status' => 2
    ];
    return $callback;
  }
}
