<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataGolonganPangkatController extends Controller
{
  public function getJenisGolPang(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_jenis_pangkat')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function getDaftarGolPang(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_daftar_pangkat')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataGolPang($idPegawai, $idDataGolPang=null, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($idDataGolPang === null) {
      $data = DB::table('m_pegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->join('m_daftar_pangkat', 'm_data_pangkat.idDaftarPangkat', '=', 'm_daftar_pangkat.id')->whereIn('m_data_pangkat.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_pangkat.idUsulanHasil', '=', 1],
        ['m_data_pangkat.idUsulan', '=', 1],
      ])->orderBy('m_daftar_pangkat.id', 'desc')->get([
        'm_data_pangkat.id',
        'm_daftar_pangkat.golongan',
        'm_daftar_pangkat.pangkat'
      ]);
    } else {
      $data = DB::table('m_pegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->leftJoin('m_dokumen', 'm_data_pangkat.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_pangkat.id', '=', $idDataGolPang],
      ])->get([
        'm_data_pangkat.*',
        'm_dokumen.dokumen'
      ]);
    }
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataGolPang($id=NULL, Request $request) {
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
      ['nama', '=', "DOK_SK_PANGKAT_$nip"]
      ])->get(), true);
    if (count($dokumenSearch) === 0) {
      $dokumen = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_SK_PANGKAT_$nip",
        'dokumen' => $message['dokumen'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    } else {
      foreach ($dokumenSearch as $key => $value) {
        $dokumen = $value['id'];
      }
    }

    $data = DB::table('m_data_pangkat')->insert([
      'id' => NULL,
      'idJenisPangkat' => $message['idJenisPangkat'],
      'idDaftarPangkat' => $message['idDaftarPangkat'],
      'masaKerjaTahun' => $message['masaKerjaTahun'],
      'masaKerjaBulan' => $message['masaKerjaBulan'],
      'nomorDokumen' => $message['nomorDokumen'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'tmt' => $message['tmt'],
      'nomorBkn' => $message['nomorBkn'],
      'tanggalBkn' => $message['tanggalBkn'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'idDataPangkatUpdate' => $id,
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
