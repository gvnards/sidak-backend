<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapSinkronController extends Controller
{
  public function getRekapSinkron(Request $request, $idPegawai=NULL) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = json_decode(DB::table('m_pegawai')
    ->leftJoin('m_data_status_kepegawaian', 'm_pegawai.id', '=', 'm_data_status_kepegawaian.idPegawai')
    ->leftJoin('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')
    ->leftJoin('m_data_cpns_pns', 'm_pegawai.id', '=', 'm_data_cpns_pns.idPegawai')
    ->leftJoin('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')
    ->leftJoin('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')
    ->leftJoin('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')
    ->groupBy([
      'm_pegawai.nip'
    ])
    ->where(function($query){
      $query->whereNotIn('m_data_status_kepegawaian.idDaftarStatusKepegawaian', [8,9,10,11,12,13,14])
      ->orWhere([
        ['m_data_status_kepegawaian.id', '=', NULL]
      ]);
    })
    ->where([
      $idPegawai === NULL ? [NULL] : ['m_pegawai.id', '=', $idPegawai]
    ])
    ->get([
      'm_pegawai.id as id',
      'm_pegawai.nip as nip',
      'm_data_pribadi.id as idDataPribadi',
      'm_data_cpns_pns.id as idDataCpnsPns',
      'm_data_pendidikan.id as idDataPendidikan',
      'm_data_pangkat.id as idDataPangkat',
      'm_data_jabatan.id as idDataJabatan',
    ]), true);
    $dt = [];
    $dtPppk = [];
    foreach ($data as $key => $d) {
      if ($d['idDataPribadi'] === null || $d['idDataCpnsPns'] === null || $d['idDataPendidikan'] === null || $d['idDataPangkat'] === null || $d['idDataJabatan'] === null) {
        if (intval($d['nip'][12].$d['nip'][13]) === 21) array_push($dtPppk, $d);
        else array_push($dt, $d);
      }
    }
    $dt = array_merge($dt, $dtPppk);
    return [
      'message' => $dt,
      'status' => count($dt) > 0 ? 2 : 3
    ];
  }
  public function onSync(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = $this->getRekapSinkron($request, $idPegawai);
    if ($data['status'] === 2) {
      $data = $data['message'][0];
      if ($data['idDataPribadi'] == null) (new ApiSiasnSyncController)->syncDataPribadi($request, $idPegawai);
      if ($data['idDataCpnsPns'] == null) (new ApiSiasnSyncController)->syncDataCpnsPns($request, $idPegawai);
      if ($data['idDataPendidikan'] == null) (new ApiSiasnSyncController)->syncPendidikanASN($request, $idPegawai);
      if ($data['idDataPangkat'] == null) (new ApiSiasnSyncController)->syncPangkatGolonganASN($request, $idPegawai);
      if ($data['idDataJabatan'] == null) {
        for ($i = 0; $i < 2; $i++) {
          (new ApiSiasnSyncController)->syncJabatanASN($request, $idPegawai);
        }
      }
    }

    return [
      'message' => 'Data berhasil disinkron',
      'status' => 2
    ];
  }
}
