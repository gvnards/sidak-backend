<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DataPendidikanController extends Controller
{
  public function getDaftarPendidikan($idTingkatPendidikan, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_daftar_pendidikan')->where([
      ['m_daftar_pendidikan.idTingkatPendidikan', '=', $idTingkatPendidikan]
      ])->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function getJenisPendidikan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_jenis_pendidikan')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getTingkatPendidikan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_tingkat_pendidikan')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataPendidikan($idPegawai, $idDataPendidikan=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($idDataPendidikan === null) {
      $data = DB::table('m_pegawai')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->join('m_tingkat_pendidikan', 'm_data_pendidikan.idTingkatPendidikan', '=', 'm_tingkat_pendidikan.id')->whereIn('m_data_pendidikan.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_pendidikan.idUsulanHasil', '=', 1],
        ['m_data_pendidikan.idUsulan', '=', 1],
      ])->orderBy('m_tingkat_pendidikan.id', 'desc')->orderBy('m_data_pendidikan.tahunLulus', 'desc')->get([
        'm_data_pendidikan.id',
        'm_data_pendidikan.namaSekolah',
        'm_tingkat_pendidikan.nama as tingkatPendidikan'
      ]);
    } else {
      $data = DB::table('m_pegawai')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->leftJoin('m_dokumen', 'm_data_pendidikan.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_pendidikan.id', '=', $idDataPendidikan],
      ])->get([
        'm_data_pendidikan.*',
        'm_dokumen.dokumen'
      ]);
    }
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataPendidikan($id=NULL, Request $request) {
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
      ['nama', '=', "DOK_IJAZAH_$nip"]
      ])->get(), true);
    if (count($dokumenSearch) === 0) {
      $dokumen = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_IJAZAH_$nip",
        'dokumen' => $message['dokumen'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    } else {
      foreach ($dokumenSearch as $key => $value) {
        $dokumen = $value['id'];
      }
    }

    $data = DB::table('m_data_pendidikan')->insert([
      'id' => NULL,
      'idJenisPendidikan' => $message['idJenisPendidikan'],
      'idTingkatPendidikan' => $message['idTingkatPendidikan'],
      'idDaftarPendidikan' => $message['idDaftarPendidikan'],
      'namaSekolah' => $message['namaSekolah'],
      'gelarDepan' => $message['gelarDepan'],
      'gelarBelakang' => $message['gelarBelakang'],
      'tanggalLulus' => $message['tanggalLulus'],
      'tahunLulus' => $message['tahunLulus'],
      'nomorDokumen' => $message['nomorDokumen'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'idDataPendidikanUpdate' => $id,
      'keteranganUsulan' => '',
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
