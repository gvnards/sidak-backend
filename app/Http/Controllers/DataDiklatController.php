<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataDiklatController extends Controller
{
  public function getJenisDiklat() {
    $data = DB::table('m_jenis_diklat')->get();
    return $data;
  }

  public function getDaftarDiklat() {
    $data = DB::table('m_daftar_diklat')->get();
    return $data;
  }

  public function getDaftarInstansiDiklat() {
    $data = DB::table('m_daftar_instansi_diklat')->get();
    return $data;
  }

  public function getDataDiklat(Request $request, $idPegawai, $idDataDiklat=null) {
    if($idDataDiklat === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return $this->encrypt($username, json_encode([
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ]));
      $data = DB::table('m_pegawai')->join('m_data_diklat', 'm_pegawai.id', '=', 'm_data_diklat.idPegawai')->join('m_jenis_diklat', 'm_data_diklat.idJenisDiklat', '=', 'm_jenis_diklat.id')->join('m_daftar_diklat', 'm_data_diklat.idDaftarDiklat', '=', 'm_daftar_diklat.id')->whereIn('m_data_diklat.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_diklat.idUsulanHasil', '=', 1],
        ['m_data_diklat.idUsulan', '=', 1],
      ])->get([
        'm_data_diklat.id',
        'm_jenis_diklat.nama as jenisDiklat',
        'm_daftar_diklat.nama as daftarDiklat',
        'm_data_diklat.namaDiklat as namaDiklat'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_diklat', 'm_pegawai.id', '=', 'm_data_diklat.idPegawai')->where([
        ['m_data_diklat.id', '=', $idDataDiklat],
      ])->get([
        'm_data_diklat.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'diklat', 'pdf');
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataDiklat($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_diklat')->where([
        ['idDataDiklatUpdate', '=', $id],
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
      'nama' => "DOK_SERTIFIKAT_DIKLAT_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SERTIFIKAT_DIKLAT_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'diklat');

    $data = DB::table('m_data_diklat')->insert([
      'id' => NULL,
      'idJenisDiklat' => $message['idJenisDiklat'],
      'idDaftarDiklat' => $message['idDaftarDiklat'],
      'namaDiklat' => $message['namaDiklat'],
      'lamaDiklat' => $message['lamaDiklat'],
      'tanggalDiklat' => $message['tanggalDiklat'],
      'tanggalSelesaiDiklat' => $message['tanggalSelesaiDiklat'],
      'idDaftarInstansiDiklat' => $message['idDaftarInstansiDiklat'],
      'institusiPenyelenggara' => $message['institusiPenyelenggara'],
      'nomorDokumen' => $message['nomorDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataDiklatUpdate' => $id,
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

  public function getDataDiklatCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $jenisDiklat = $this->getJenisDiklat();
    $daftarDiklat = $this->getDaftarDiklat();
    $daftarInstansiDiklat = $this->getDaftarInstansiDiklat();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('diklat/kursus');
    $callback = [
      'message' => [
        'jenisDiklat' => $jenisDiklat,
        'daftarDiklat' => $daftarDiklat,
        'daftarInstansiDiklat' => $daftarInstansiDiklat,
        'dokumenKategori' => $dokumenKategori,
      ],
      'status' => 2
    ];

    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataDiklatDetail(Request $request, $idPegawai, $idDataDiklat) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $jenisDiklat = $this->getJenisDiklat();
    $daftarDiklat = $this->getDaftarDiklat();
    $daftarInstansiDiklat = $this->getDaftarInstansiDiklat();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('diklat/kursus');
    $dataDiklat = $this->getDataDiklat($request, $idPegawai, $idDataDiklat);
    $callback = [
      'message' => [
        'jenisDiklat' => $jenisDiklat,
        'daftarDiklat' => $daftarDiklat,
        'daftarInstansiDiklat' => $daftarInstansiDiklat,
        'dataDiklat' => $dataDiklat,
        'dokumenKategori' => $dokumenKategori,
      ],
      'status' => 2
    ];

    return $this->encrypt($username, json_encode($callback));
  }
}
