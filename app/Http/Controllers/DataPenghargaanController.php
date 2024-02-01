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
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'penghargaan', 'pdf');
      $data[0]['dokumen'] = '';
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
    // kondisi bypass
    $isByPass = $this->isUsernameGetByPass($username);
    if ($isByPass) {
      $dt = json_decode(DB::table('m_data_penghargaan')->where([
        'idDokumen' => $dokumen,
        'idPegawai' => $message['idPegawai'],
        'idUsulan' => $idDataPenghargaan == NULL ? 1 : 2,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ])->get(), true);
      $dtUpdate = $this->updateDataPenghargaan($dt[0]['id'], [
      'idUsulanStatus' => 3,
      'idUsulanHasil' => 1,
      'keteranganUsulan' => ''
      ], $isByPass);
      if ($dtUpdate['status'] === 4) {
        return [
          'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
          'status' => 3
        ];
      }
      $callback = $dtUpdate;
    }
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

  public function updateDataPenghargaan($idUsulan, $message, $isByPass=false) {
    $usulan = json_decode(DB::table('m_data_penghargaan')->where([
      ['id', '=', $idUsulan]
    ])->get()->toJson(), true)[0];
    if (intval($usulan['idUsulanStatus']) !== 1) {
      /// data sudah diverifikasi
      return [
        'status' => 4
      ];
    }
    if (intval($usulan['idUsulan']) == 1 && intval($message['idUsulanHasil']) == 1) {
      $response = (new ApiSiasnSyncController)->insertRiwayatPenghargaan($idUsulan);
      if (!$response['success']) {
        $callback = [
          'message' => $response['message'],
          'status' => 3
        ];
        if ($isByPass) {
          /// delete ketika ada masalah
          DB::table('m_data_penghargaan')->where([
            ['id', '=', $idUsulan]
          ])->delete();
        }
        return $callback;
      } else {
        DB::table('m_data_penghargaan')->where('id', '=', $idUsulan)->update([
          'idBkn' => $response['mapData']['rwPenghargaanId'],
        ]);
        $dokumen = json_decode(DB::table('m_dokumen')->where([
          ['id', '=', $usulan['idDokumen']]
        ])->get()->toJson(), true)[0];
        /// Belum ada upload dokumennya di WS
        // (new ApiSiasnController)->insertDokumenRiwayat($request, $response['mapData']['rwPenghargaanId'], 872, 'jabatan', $dokumen['nama'], 'pdf');
      }
    }
    $newData = json_decode(DB::table('m_data_penghargaan')->where('id', '=', $idUsulan)->get(), true);
    $idUpdate = $newData[0]['idDataPenghargaanUpdate'];
    $data = DB::table('m_data_penghargaan')->where('id', '=', $idUsulan)->update([
      'idUsulanStatus' => $message['idUsulanStatus'],
      'idUsulanHasil' => $message['idUsulanHasil'],
      'keteranganUsulan' => $message['keteranganUsulan'],
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    if ($idUpdate != null) {
      if (intval($message['idUsulanHasil']) == 1) {
        $oldData = json_decode(DB::table('m_data_penghargaan')->where('id', '=', $idUpdate)->get(), true)[0];
        foreach ($newData as $key => $value) {
          $data = DB::table('m_data_penghargaan')->where('id', '=', $idUpdate)->update([
            'tahunPenghargaan' => $value['tahunPenghargaan'],
            'idDaftarJenisPenghargaan' => $value['idDaftarJenisPenghargaan'],
            'tanggalDokumen' => $value['tanggalDokumen'],
            'nomorDokumen' => $value['nomorDokumen'],
            'idDokumen' => $value['idDokumen'],
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ]);
        }
        DB::table('m_data_penghargaan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        if ($oldData['idDokumen'] !== null) {
          $this->deleteDokumen($oldData['idDokumen'], 'penghargaan', 'pdf');
        }
      } else {
        $getData = $newData[0];
        DB::table('m_data_penghargaan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        $this->deleteDokumen($getData['idDokumen'], 'penghargaan', 'pdf');
      }
    }
    return [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
  }
}
