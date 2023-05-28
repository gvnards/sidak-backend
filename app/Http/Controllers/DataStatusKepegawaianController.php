<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataStatusKepegawaianController extends Controller
{
  public function getDaftarStatusKepegawaian(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_daftar_status_kepegawaian')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function getDataStatusKepegawaian($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_data_status_kepegawaian')->where([['idPegawai', '=', $idPegawai]])->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataStatusKepegawaian(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $hasBeenSetting = json_decode(DB::table('m_data_status_kepegawaian')->where([['idPegawai', '=', $message['idPegawai']]])->get(), true);
    if(count($hasBeenSetting) > 0) {
      DB::table('m_data_status_kepegawaian')->where([['idPegawai', '=', $message['idPegawai']]])->update([
        'idDaftarStatusKepegawaian' => $message['idDaftarStatusKepegawaian'],
        'tmt' => $message['tmt']
      ]);
    } else {
      DB::table('m_data_status_kepegawaian')->insert([
        'id' => NULL,
        'idPegawai' => $message['idPegawai'],
        'idDaftarStatusKepegawaian' => $message['idDaftarStatusKepegawaian'],
        'tmt' => $message['tmt'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
    $callback = [
      'message' => 'Status kepegawaian berhasil disetting.',
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
