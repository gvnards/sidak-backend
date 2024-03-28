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
    $userAdmin = json_decode(DB::table('m_admin')->join('m_unit_organisasi', 'm_admin.unitOrganisasi', '=', 'm_unit_organisasi.kodeKomponen')->where([
      ['m_admin.username', '=', $username]
    ])->get([
      'm_unit_organisasi.kodeKomponen as kodeKomponen',
      'm_unit_organisasi.nama as namaUnitOrganisasi'
    ]), true);
    if ($idAppRoleUser === 1) {
      $data = json_decode(DB::table('v_short_brief')->groupBy('id')->get([
        'id as id',
        'nama as nama',
        'nip as nip',
        'jabatan as jabatan',
        'kodeKomponen as kodeKomponen',
        'unitOrganisasi as unitOrganisasi',
        'golongan as golongan',
        'pangkat as pangkat'
      ]), true);
    } else {
      $data = json_decode(DB::table('v_short_brief')->where('kodeKomponen', 'LIKE', $userAdmin[0]['kodeKomponen'].'%')->groupBy('id')->get([
        'id as id',
        'nama as nama',
        'nip as nip',
        'jabatan as jabatan',
        'kodeKomponen as kodeKomponen',
        'unitOrganisasi as unitOrganisasi',
        'golongan as golongan',
        'pangkat as pangkat'
      ]), true);
    }
    $listKodeKomponen = [];
    foreach ($data as $value) {
      $kdExplode = explode(".", $value['kodeKomponen']);
      $countKdExplode = count($kdExplode);
      for ($i = 0; $i < $countKdExplode; $i++) {
        if (count($listKodeKomponen) === 0) {
          array_push($listKodeKomponen, implode(".", $kdExplode));
        } else {
          if (count($kdExplode) === 0) break;
          $kdImplode = implode(".", $kdExplode);
          $isHasKd = false;
          foreach ($listKodeKomponen as $listKd) {
            if ($kdImplode === $listKd) $isHasKd = true;
          }
          if (!$isHasKd) {
            array_push($listKodeKomponen, $kdImplode);
          }
          array_pop($kdExplode);
        }
      }
    }
    $allUnors = (new DataJabatanController)->getAllUnor(DB::table('m_unit_organisasi')->whereIn('kodeKomponen', $listKodeKomponen)->where([
      ['idBkn', '!=', ''],
      ['kodeKomponen', 'NOT LIKE', '-%']
    ])->get());
    for ($i = 0; $i < count($data); $i++) {
      if (str_contains($data[$i]['kodeKomponen'], "-")) {
        $data[$i]['unitOrganisasi'] = "(Unit organisasi tidak ada di dalam database. Silahkan update atau konsultasi dengan BKPSDM.)";
        continue;
      }
      for ($j = 0; $j < count($allUnors); $j++) {
        if ($data[$i]['kodeKomponen'] === $allUnors[$j]['kodeKomponen']) {
          $data[$i]['unitOrganisasi'] = $allUnors[$j]['nama'];
          break;
        }
      }
    }
    $callback = [
      'message' => [
        'pegawai' => $data,
        'unitOrganisasi' => $userAdmin[0]['namaUnitOrganisasi']
      ],
      'status' => 2
    ];
    return $callback;
  }
}
