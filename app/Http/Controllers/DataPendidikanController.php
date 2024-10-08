<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DataPendidikanController extends Controller
{
  public function getDaftarPendidikan() {
    $data = json_decode(DB::table('m_daftar_pendidikan')->get(), true);
    return $data;
  }
  public function getJenisPendidikan() {
    $data = json_decode(DB::table('m_jenis_pendidikan')->get(), true);
    return $data;
  }

  public function getTingkatPendidikan() {
    $data = json_decode(DB::table('m_tingkat_pendidikan')->get(), true);
    return $data;
  }

  public function getDataPendidikan(Request $request, $idPegawai, $idDataPendidikan=NULL) {
    if($idDataPendidikan === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
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
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pendidikan', 'pdf');
      // $data[0]['dokumenTranskrip'] = $this->getBlobDokumen($data[0]['idDokumenTranskrip'], 'pendidikan', 'pdf');
      $data[0]['dokumen'] = '';
      $data[0]['dokumenTranskrip'] = '';
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function insertDataPendidikan($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_pendidikan')->where([
        ['idDataPendidikanUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
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
    // kondisi bypass
    $isByPass = $this->isUsernameGetByPass($username);
    if ($isByPass) {
      $dt = json_decode(DB::table('m_data_pendidikan')->where([
        'idDokumen' => $dokumen,
        'idPegawai' => $message['idPegawai'],
        'idUsulan' => $id == NULL ? 1 : 2,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ])->get(), true);
      $dtUpdate = $this->updateDataPendidikan($dt[0]['id'], [
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

  public function getDataPendidikanCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $daftarPendidikan = $this->getDaftarPendidikan();
    $jenisPendidikan = $this->getJenisPendidikan();
    $tingkatPendidikan = $this->getTingkatPendidikan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('pendidikan');
    $callback = [
      'message' => [
        'daftarPendidikan' => $daftarPendidikan,
        'jenisPendidikan' => $jenisPendidikan,
        'tingkatPendidikan' => $tingkatPendidikan,
        'dokumenKategori' => $dokumenKategori,
      ],
      'status' => 1
    ];
    return $callback;
  }

  public function getDataPendidikanDetail(Request $request, $idPegawai, $idDataPendidikan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $daftarPendidikan = $this->getDaftarPendidikan();
    $jenisPendidikan = $this->getJenisPendidikan();
    $tingkatPendidikan = $this->getTingkatPendidikan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('pendidikan');
    $dataPendidikan = $this->getDataPendidikan($request, $idPegawai, $idDataPendidikan);
    $callback = [
      'message' => [
        'daftarPendidikan' => $daftarPendidikan,
        'jenisPendidikan' => $jenisPendidikan,
        'tingkatPendidikan' => $tingkatPendidikan,
        'dataPendidikan' => $dataPendidikan,
        'dokumenKategori' => $dokumenKategori,
      ],
      'status' => 1
    ];
    return $callback;
  }

  public function updateDataPendidikan($idUsulan, $message) {
    $newData = json_decode(DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->get(), true);
    if (intval($newData[0]['idUsulanStatus']) !== 1) {
      /// data sudah diverifikasi
      return [
        'status' => 4
      ];
    }
    $idUpdate = $newData[0]['idDataPendidikanUpdate'];
    $data = DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
      'idUsulanStatus' => $message['idUsulanStatus'],
      'idUsulanHasil' => $message['idUsulanHasil'],
      'keteranganUsulan' => $message['keteranganUsulan'],
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    if ($idUpdate != null) {
      if (intval($message['idUsulanHasil']) == 1) {
        $oldData = json_decode(DB::table('m_data_pendidikan')->where('id', '=', $idUpdate)->get(), true)[0];
        foreach ($newData as $key => $value) {
          $data = DB::table('m_data_pendidikan')->where('id', '=', $idUpdate)->update([
            'idJenisPendidikan' => $value['idJenisPendidikan'],
            'idTingkatPendidikan' => $value['idTingkatPendidikan'],
            'idDaftarPendidikan' => $value['idDaftarPendidikan'],
            'namaSekolah' => $value['namaSekolah'],
            'gelarDepan' => $value['gelarDepan'],
            'gelarBelakang' => $value['gelarBelakang'],
            'tanggalLulus' => $value['tanggalLulus'],
            'tahunLulus' => $value['tahunLulus'],
            'nomorDokumen' => $value['nomorDokumen'],
            'tanggalDokumen' => $value['tanggalDokumen'],
            'idDokumen' => $value['idDokumen'],
            'idDokumenTranskrip' => $value['idDokumenTranskrip'],
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ]);
        }
        DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1,
          'idDokumenTranskrip' => 1,
        ]);
        if ($oldData['idDokumen'] !== null) {
          $this->deleteDokumen($oldData['idDokumen'], 'pendidikan', 'pdf');
        }
        if ($oldData['idDokumenTranskrip'] !== null) {
          $this->deleteDokumen($oldData['idDokumenTranskrip'], 'pendidikan', 'pdf');
        }
      } else {
        $getData = $newData[0];
        DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1,
          'idDokumenTranskrip' => 1
        ]);
        $this->deleteDokumen($getData['idDokumen'], 'pendidikan', 'pdf');
        $this->deleteDokumen($getData['idDokumenTranskrip'], 'pendidikan', 'pdf');
      }
    }
    return [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
  }
}
