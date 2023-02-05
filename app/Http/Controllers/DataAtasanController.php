<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataAtasanController extends Controller
{
  public function getDaftarAtasan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('v_pegawai')->where([
      ['eselon', '!=', 'NULL']
    ])->get([
      'id',
      'nama',
      'nip'
    ]);
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function getDataAtasan($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_data_atasan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataBawahan($idAtasan, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_data_atasan')->where([
      ['idAtasan', '=', $idAtasan]
    ])->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataAtasan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $hasBeenSetting = json_decode(DB::table('m_data_atasan')->where([['idPegawai', '=', $message['idPegawai']]])->get(), true);
    if(count($hasBeenSetting) > 0) {
      DB::table('m_data_atasan')->where([['idPegawai', '=', $message['idPegawai']]])->update([
        'idAtasan' => $message['idAtasan']
      ]);
    } else {
      DB::table('m_data_atasan')->insert([
        'id' => NULL,
        'idPegawai' => $message['idPegawai'],
        'idAtasan' => $message['idAtasan'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
    $callback = [
      'message' => 'Atasan berhasil disetting.',
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
