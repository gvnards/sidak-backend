<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPribadiController extends Controller
{
  public function getDataPribadi($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->where([
      ['m_pegawai.id', '=', $idPegawai]
    ])->get([
      'm_data_pribadi.*',
      'm_pegawai.nip as nip'
    ]);
    $callback = [
      'message' => count($data) == 1 ? $data : 'Data tidak ditemukan.',
      'status' => count($data) == 1 ? 2 : 3
    ];
    return $callback;
  }

  public function updateDataPribadi($idDataPribadi, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_data_pribadi')->where([
      ['id', '=', $idDataPribadi]
    ])->update([
      'alamat' => $message['alamat'],
      'nomorHp' => $message['nomorHp'],
      'email' => $message['email'],
      'npwp' => $message['npwp'],
      'bpjs' => $message['bpjs']
    ]);
    $callback = [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }
}
