<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataGolonganPangkatController extends Controller
{
  public function getJenisGolPang() {
    $data = DB::table('m_jenis_pangkat')->get();
    return $data;
  }
  public function getDaftarGolPang() {
    $data = DB::table('m_daftar_pangkat')->get();
    return $data;
  }

  public function getDataGolPang(Request $request, $idPegawai, $idDataGolPang=null) {
    if($idDataGolPang === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
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
      $data = json_decode(DB::table('m_pegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->where([
        ['m_data_pangkat.id', '=', $idDataGolPang],
      ])->get([
        'm_data_pangkat.*'
      ]), true);
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pangkat', 'pdf');
      $data[0]['dokumen'] = '';
      return $data[0];
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function insertDataGolPang($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_pangkat')->where([
        ['idDataPangkatUpdate', '=', $id],
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
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_SK_PANGKAT_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SK_PANGKAT_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'pangkat');

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
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }

  public function getDataGolPangCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jenisGolonganPangkat = $this->getJenisGolPang();
    $daftarGolonganPangkat = $this->getDaftarGolPang();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('pangkat');
    $callback = [
      'message' => [
        'jenisGolonganPangkat' => $jenisGolonganPangkat,
        'daftarGolonganPangkat' => $daftarGolonganPangkat,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];
    return $callback;
  }

  public function getDataGolPangDetail(Request $request, $idPegawai, $idDataGolPang) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jenisGolonganPangkat = $this->getJenisGolPang();
    $daftarGolonganPangkat = $this->getDaftarGolPang();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('pangkat');
    $dataGolonganPangkat = $this->getDataGolPang($request, $idPegawai, $idDataGolPang);
    $callback = [
      'message' => [
        'jenisGolonganPangkat' => $jenisGolonganPangkat,
        'daftarGolonganPangkat' => $daftarGolonganPangkat,
        'dataGolonganPangkat' => $dataGolonganPangkat,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];
    return $callback;
  }
}
