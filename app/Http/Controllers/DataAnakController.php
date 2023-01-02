<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataAnakController extends Controller
{
  public function getDataAnak($idPegawai, $idDataAnak=null, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($idDataAnak === null) {
      $data = DB::table('m_pegawai')->join('m_data_anak', 'm_pegawai.id', '=', 'm_data_anak.idPegawai')->join('m_status_anak', 'm_data_anak.idStatusAnak', '=', 'm_status_anak.id')->whereIn('m_data_anak.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_anak.idUsulanHasil', '=', 1],
        ['m_data_anak.idUsulan', '=', 1]
      ])->get([
        'm_data_anak.id',
        'm_data_anak.nama',
        'm_status_anak.nama as statusAnak'
      ]);
    } else {
      $data = DB::table('m_pegawai')->join('m_data_anak', 'm_pegawai.id', '=', 'm_data_anak.idPegawai')->leftJoin('m_dokumen', 'm_data_anak.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_anak.id', '=', $idDataAnak],
      ])->get([
        'm_data_anak.*',
        'm_dokumen.dokumen'
      ]);
    }
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getStatusAnak(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_status_anak')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataOrangTua($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $data = DB::table('m_pegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->whereIn('m_data_pasangan.idUsulanStatus', [3, 4])->where([
      ['m_pegawai.id', '=', $idPegawai],
      ['m_data_pasangan.idUsulanHasil', '=', 1],
    ])->get([
      'm_data_pasangan.id',
      'm_data_pasangan.nama',
    ]);

    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataAnak($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    // jika dokumen sama, maka gunakan yang lama, jika tidak, insert baru
    $dokumenSearch = json_decode(DB::table('m_dokumen')->where([
      ['dokumen', '=', $message['dokumen']],
      ['nama', '=', "DOK_AKTA_ANAK_$nip"]
      ])->get(), true);
    if (count($dokumenSearch) === 0) {
      $dokumen = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_AKTA_ANAK_$nip",
        'dokumen' => $message['dokumen'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    } else {
      foreach ($dokumenSearch as $key => $value) {
        $dokumen = $value['id'];
      }
    }

    $data = DB::table('m_data_anak')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'tempatLahir' => $message['tempatLahir'],
      'tanggalLahir' => $message['tanggalLahir'],
      'idOrangTua' => $message['idOrangTua'],
      'idStatusAnak' => $message['idStatusAnak'],
      'nomorDokumen' => $message['nomorDokumen'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataAnakUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method." : "Data gagal diusulkan untuk $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
