<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPasanganController extends Controller
{
  public function getDataPasangan(Request $request, $idPegawai, $idDataPasangan=null) {
    if($idDataPasangan === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
      $data = DB::table('m_pegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->whereIn('m_data_pasangan.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_pasangan.idUsulanHasil', '=', 1],
        ['m_data_pasangan.idUsulan', '=', 1],
      ])->get([
        'm_data_pasangan.id',
        'm_data_pasangan.nama',
        'm_status_perkawinan.nama as statusPerkawinan'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->where([
        ['m_data_pasangan.id', '=', $idDataPasangan],
      ])->get([
        'm_data_pasangan.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pasangan', 'pdf');
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  private function getStatusPerkawinan() {
    $data = json_decode(DB::table('m_status_perkawinan')->get(), true);
    return $data;
  }

  public function insertDataPasangan($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_pasangan')->where([
        ['idDataPasanganUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    } else {
      // check ketika sudah ada data yg ditambahkan dan belum diapprove, return info tunggu disahkan
      $countIsAny = count(json_decode(DB::table('m_data_pasangan')->where([
        ['m_data_pasangan.idPegawai', '=', intval($message['idPegawai'])],
        ['m_data_pasangan.idUsulan', '=', 1],
        ['m_data_pasangan.idUsulanHasil', '=', 3]
      ])->get()));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, Data Pasangan sudah ada yang ditambahkan tetapi belum diverifikasi.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    }
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_AKTA_PERKAWINAN_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_AKTA_PERKAWINAN_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'pasangan');

    $data = DB::table('m_data_pasangan')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'tempatLahir' => $message['tempatLahir'],
      'tanggalLahir' => $message['tanggalLahir'],
      'tanggalStatusPerkawinan' => $message['tanggalStatusPerkawinan'],
      'nomorDokumen' => $message['nomorDokumen'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'idStatusPerkawinan' => $message['idStatusPerkawinan'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataPasanganUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }

  public function getDataPasanganCreated(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $dataStatusPerkawinan = $this->getStatusPerkawinan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('perkawinan');
    $callback = [
      'message' => [
        'dataStatusPerkawinan' => $dataStatusPerkawinan,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function getDataPasanganDetail(Request $request, $idPegawai, $idDataPasangan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $dataStatusPerkawinan = $this->getStatusPerkawinan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('perkawinan');
    $dataPasangan = $this->getDataPasangan($request, $idPegawai, $idDataPasangan);
    $callback = [
      'message' => [
        'dataStatusPerkawinan' => $dataStatusPerkawinan,
        'dokumenKategori' => $dokumenKategori,
        'dataPasangan' => $dataPasangan
      ],
      'status' => 2
    ];

    return $callback;
  }
}
