<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPenghargaanController extends Controller
{
  private function getDaftarJenisPenghargaan() {
    $data = DB::table('m_daftar_jenis_penghargaan')->get([
      'id',
      'jenisPenghargaan'
    ]);
    return $data;
  }

  public function getDataPenghargaan(Request $request, $idPegawai, $idDataPenghargaan=NULL) {
    if ($idDataPenghargaan == NULL) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
      $data = DB::table('m_data_penghargaan')->join('m_daftar_jenis_penghargaan', 'm_data_penghargaan.idDaftarJenisPenghargaan', '=', 'm_daftar_jenis_penghargaan.id')->whereIn('m_data_penghargaan.idUsulanStatus', [3, 4])->where([
        ['m_data_penghargaan.idPegawai', '=', $idPegawai],
        ['m_data_penghargaan.idUsulanHasil', '=', 1],
        ['m_data_penghargaan.idUsulan', '=', 1],
      ])->orderBy('m_data_penghargaan.tahunPenghargaan', 'desc')->get([
        'm_data_penghargaan.id',
        'm_data_penghargaan.tahunPenghargaan AS tahun',
        'm_daftar_jenis_penghargaan.jenisPenghargaan AS penghargaan',
      ]);
    } else {
      $data = json_decode(DB::table('m_data_penghargaan')->where([
        ['id', '=', $idDataPenghargaan]
      ])->get(), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'penghargaan', 'pdf');
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function insertDataPenghargaan(Request $request, $idDataPenghargaan=NULL) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if ($idDataPenghargaan !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_penghargaan')->where([
        ['idDataPenghargaanUpdate', '=', $idDataPenghargaan],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    }
    $message = json_decode($this->decrypt($username, $request->message), true);$nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_PENGHARGAAN_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_PENGHARGAAN_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'penghargaan');

    $data = DB::table('m_data_penghargaan')->insert([
      'id' => NULL,
      'tahunPenghargaan' => $message['tahunPenghargaan'],
      'idDaftarJenisPenghargaan' => $message['idDaftarJenisPenghargaan'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $idDataPenghargaan == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataPenghargaanUpdate' => $idDataPenghargaan,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $idDataPenghargaan == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }

  public function getDataPenghargaanCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jenisPenghargaan = $this->getDaftarJenisPenghargaan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('penghargaan');
    $callback = [
      'message' => [
        'jenisPenghargaan' => $jenisPenghargaan,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function getDataPenghargaanDetail(Request $request, $idPegawai, $idDataPenghargaan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jenisPenghargaan = $this->getDaftarJenisPenghargaan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('penghargaan');
    $dataPenghargaan = $this->getDataPenghargaan($request, $idPegawai, $idDataPenghargaan);
    $callback = [
      'message' => [
        'jenisPenghargaan' => $jenisPenghargaan,
        'dokumenKategori' => $dokumenKategori,
        'dataPenghargaan' => $dataPenghargaan
      ],
      'status' => 2
    ];

    return $callback;
  }
}
