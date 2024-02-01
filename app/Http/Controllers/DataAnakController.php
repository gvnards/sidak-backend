<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataAnakController extends Controller
{
  public function getDataAnak(Request $request, $idPegawai, $idDataAnak=null) {
    if($idDataAnak === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
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
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'anak', 'pdf');
      $data[0]['dokumen'] = '';
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  private function getStatusAnak() {
    $data = json_decode(DB::table('m_status_anak')->get(), true);
    return $data;
  }

  private function getDataOrangTua($idPegawai) {
    $data = json_decode(DB::table('m_pegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->whereIn('m_data_pasangan.idUsulanStatus', [3, 4])->where([
      ['m_pegawai.id', '=', $idPegawai],
      ['m_data_pasangan.idUsulan', '=', 1],
      ['m_data_pasangan.idUsulanHasil', '=', 1],
    ])->get([
      'm_data_pasangan.id',
      'm_data_pasangan.nama',
    ]), true);
    return $data;
  }

  public function insertDataAnak($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_anak')->where([
        ['idDataAnakUpdate', '=', $id],
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
      $countIsAny = count(json_decode(DB::table('m_data_anak')->where([
        ['m_data_anak.idPegawai', '=', intval($message['idPegawai'])],
        ['m_data_anak.idUsulan', '=', 1],
        ['m_data_anak.idUsulanHasil', '=', 3]
      ])->get()));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, Data Anak sudah ada yang ditambahkan tetapi belum diverifikasi.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
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
    // kondisi bypass
    $isByPass = $this->isUsernameGetByPass($username);
    if ($isByPass) {
      $dt = json_decode(DB::table('m_data_anak')->where([
        'idDokumen' => $dokumen,
        'idPegawai' => $message['idPegawai'],
        'idUsulan' => $id == NULL ? 1 : 2,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ])->get(), true);
      $dtUpdate = $this->updateDataAnak($dt[0]['id'], [
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

  public function getDataAnakCreated(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $dataOrangTua = $this->getDataOrangTua($idPegawai);
    $dataStatusAnak = $this->getStatusAnak();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('kelahiran anak');
    $callback = [
      'message' => [
        'dataOrangTua' => $dataOrangTua,
        'dataStatusAnak' => $dataStatusAnak,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function getDataAnakDetail(Request $request, $idPegawai, $idDataAnak) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $dataOrangTua = $this->getDataOrangTua($idPegawai);
    $dataStatusAnak = $this->getStatusAnak();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('kelahiran anak');
    $dataAnak = $this->getDataAnak($request, $idPegawai, $idDataAnak);
    $callback = [
      'message' => [
        'dataOrangTua' => $dataOrangTua,
        'dataStatusAnak' => $dataStatusAnak,
        'dokumenKategori' => $dokumenKategori,
        'dataAnak' => $dataAnak
      ],
      'status' => 2
    ];

    return $callback;
  }
  public function updateDataAnak($idUsulan, $message) {
    /**
     * kalo isByPass = true
     * message = [
     * 'idUsulanStatus' => 3,
     * 'idUsulanHasil' => 1,
     * 'keteranganUsulan' => ''
     * ]
     */
    $newData = json_decode(DB::table('m_data_anak')->where('id', '=', $idUsulan)->get(), true);
    if (intval($newData[0]['idUsulanStatus']) !== 1) {
      /// data sudah diverifikasi
      return [
        'status' => 4
      ];
    }
    $idUpdate = $newData[0]['idDataAnakUpdate'];
    $data = DB::table('m_data_anak')->where('id', '=', $idUsulan)->update([
      'idUsulanStatus' => $message['idUsulanStatus'],
      'idUsulanHasil' => $message['idUsulanHasil'],
      'keteranganUsulan' => $message['keteranganUsulan'],
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    if ($idUpdate != null) {
      if (intval($message['idUsulanHasil']) == 1) {
        $oldData = json_decode(DB::table('m_data_anak')->where('id', '=', $idUpdate)->get(), true)[0];
        foreach ($newData as $key => $value) {
          $data = DB::table('m_data_anak')->where('id', '=', $idUpdate)->update([
            'nama' => $value['nama'],
            'tempatLahir' => $value['tempatLahir'],
            'tanggalLahir' => $value['tanggalLahir'],
            'nomorDokumen' => $value['nomorDokumen'],
            'tanggalDokumen' => $value['tanggalDokumen'],
            'idOrangTua' => $value['idOrangTua'],
            'idStatusAnak' => $value['idStatusAnak'],
            'idDokumen' => $value['idDokumen'],
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ]);
        }
        DB::table('m_data_anak')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        if ($oldData['idDokumen'] !== null) {
          $this->deleteDokumen($oldData['idDokumen'], 'anak', 'pdf');
        }
      } else {
        $getData = $newData[0];
        DB::table('m_data_anak')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        $this->deleteDokumen($getData['idDokumen'], 'anak', 'pdf');
      }
    }
    return [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
  }
}
