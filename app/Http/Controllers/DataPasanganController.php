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
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pasangan', 'pdf');
      $data[0]['dokumen'] = '';
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
    // kondisi bypass
    $isByPass = $this->isUsernameGetByPass($username);
    if ($isByPass) {
      $dt = json_decode(DB::table('m_data_pasangan')->where([
        'idDokumen' => $dokumen,
        'idPegawai' => $message['idPegawai'],
        'idUsulan' => $id == NULL ? 1 : 2,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ])->get(), true);
      $dtUpdate = $this->updateDataPasangan($dt[0]['id'], [
      'idUsulanStatus' => 3,
      'idUsulanHasil' => 1,
      'keteranganUsulan' => ''
      ]);
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

  public function updateDataPasangan($idUsulan, $message) {
    $newData = json_decode(DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->get(), true);
    if (intval($newData[0]['idUsulanStatus']) !== 1) {
      /// data sudah diverifikasi
      return [
        'status' => 4
      ];
    }
    $data = DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
      'idUsulanStatus' => $message['idUsulanStatus'],
      'idUsulanHasil' => $message['idUsulanHasil'],
      'keteranganUsulan' => $message['keteranganUsulan'],
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $idUpdate = $newData[0]['idDataPasanganUpdate'];
    if ($idUpdate != null) {
      if (intval($message['idUsulanHasil']) == 1) {
        $oldData = json_decode(DB::table('m_data_pasangan')->where('id', '=', $idUpdate)->get(), true)[0];
        foreach ($newData as $key => $value) {
          $data = DB::table('m_data_pasangan')->where('id', '=', $idUpdate)->update([
            'nama' => $value['nama'],
            'tempatLahir' => $value['tempatLahir'],
            'tanggalLahir' => $value['tanggalLahir'],
            'tanggalStatusPerkawinan' => $value['tanggalStatusPerkawinan'],
            'nomorDokumen' => $value['nomorDokumen'],
            'tanggalDokumen' => $value['tanggalDokumen'],
            'idStatusPerkawinan' => $value['idStatusPerkawinan'],
            'idDokumen' => $value['idDokumen'],
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ]);
        }
        DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        if ($oldData['idDokumen'] !== null) {
          $this->deleteDokumen($oldData['idDokumen'], 'pasangan', 'pdf');
        }
      } else {
        $getData = $newData[0];
        DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        $this->deleteDokumen($getData['idDokumen'], 'pasangan', 'pdf');
      }
    }
    return [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
  }
}
