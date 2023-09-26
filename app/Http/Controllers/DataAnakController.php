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
      $data = json_decode(DB::table('m_pegawai')->join('m_data_anak', 'm_pegawai.id', '=', 'm_data_anak.idPegawai')->leftJoin('m_dokumen', 'm_data_anak.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_anak.id', '=', $idDataAnak],
      ])->get([
        'm_data_anak.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'anak', 'pdf');
    }
    $callback = [
      'message' => $data,
      'status' => 2
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
      'status' => 2
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
      ['m_data_pasangan.idUsulan', '=', 1],
      ['m_data_pasangan.idUsulanHasil', '=', 1],
    ])->get([
      'm_data_pasangan.id',
      'm_data_pasangan.nama',
    ]);

    $callback = [
      'message' => $data,
      'status' => 2
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
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_anak')->where([
        ['idDataAnakUpdate', '=', $id],
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
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_AKTA_ANAK_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_AKTA_ANAK_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'anak');

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
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
