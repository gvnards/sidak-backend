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
      'status' => 2
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
      'status' => 2
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
      'status' => 2
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
      $data = json_decode(DB::table('m_pegawai')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->where([
        ['m_data_pendidikan.id', '=', $idDataPendidikan],
      ])->get([
        'm_data_pendidikan.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pendidikan', 'pdf');
    }
    $callback = [
      'message' => $data,
      'status' => 2
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
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_pendidikan')->where([
        ['idDataPendidikanUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return $this->encrypt($username, json_encode([
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ]));
      }
    }
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    /// ijazah
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_IJAZAH_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_IJAZAH_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'pendidikan');
    /// transkrip
    $dokumenTranskrip = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_TRANSKRIP_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_TRANSKRIP_".$nip."_".$message['date'],$message['dokumenTranskrip'], 'pdf', 'pendidikan');

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
      'idDokumenTranskrip' => $dokumenTranskrip,
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
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
