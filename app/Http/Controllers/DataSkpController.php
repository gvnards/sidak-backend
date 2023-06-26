<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSkpController extends Controller
{
  public function getDataSkp($idPegawai, $idDataSkp=null, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($idDataSkp === null) {
      $data = DB::table('m_pegawai')->join('m_data_skp', 'm_pegawai.id', '=', 'm_data_skp.idPegawai')->whereIn('m_data_skp.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_skp.idUsulanHasil', '=', 1],
        ['m_data_skp.idUsulan', '=', 1],
      ])->get([
        'm_data_skp.id as id',
        'm_data_skp.tahun as tahun',
        'm_data_skp.nilaiPrestasiKerja as nilaiPrestasiKerja'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_skp', 'm_pegawai.id', '=', 'm_data_skp.idPegawai')->join('m_jenis_jabatan', 'm_data_skp.idJenisJabatan', '=', 'm_jenis_jabatan.id')->leftJoin('m_dokumen', 'm_data_skp.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_skp.id', '=', $idDataSkp],
      ])->get([
        'm_data_skp.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'skp', 'pdf');
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getJenisJabatan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_jenis_jabatan')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getJenisPeraturanKinerja(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_jenis_peraturan_kinerja')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getJenisStatusPejabatAtasanPenilai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_status_pejabat_atasan_penilai')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataSkp($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_skp')->where([
        ['idDataSkpUpdate', '=', $id],
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
      'nama' => "DOK_SKP_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SKP_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'skp');

    $data = DB::table('m_data_skp')->insert([
      'id' => NULL,
      'idJenisJabatan' => $message['idJenisJabatan'],
      'tahun' => $message['tahun'],
      'idJenisPeraturanKinerja' => $message['idJenisPeraturanKinerja'],
      'nilaiSkp' => $message['nilaiSkp'],
      'orientasiPelayanan' => $message['orientasiPelayanan'],
      'integritas' => $message['integritas'],
      'komitmen' => $message['komitmen'],
      'disiplin' => $message['disiplin'],
      'kerjaSama' => $message['kerjaSama'],
      'kepemimpinan' => $message['kepemimpinan'],
      'nilaiPrestasiKerja' => $message['nilaiPrestasiKerja'],
      'nilaiKonversi' => $message['nilaiKonversi'],
      'nilaiIntegrasi' => $message['nilaiIntegrasi'],
      'idStatusPejabatPenilai' => $message['idStatusPejabatPenilai'],
      'nipNrpPejabatPenilai' => $message['nipNrpPejabatPenilai'],
      'namaPejabatPenilai' => $message['namaPejabatPenilai'],
      'unitOrganisasiPejabatPenilai' => $message['unitOrganisasiPejabatPenilai'],
      'golonganPejabatPenilai' => $message['golonganPejabatPenilai'],
      'tmtGolonganPejabatPenilai' => $message['tmtGolonganPejabatPenilai'],
      'idStatusAtasanPejabatPenilai' => $message['idStatusAtasanPejabatPenilai'],
      'nipNrpAtasanPejabatPenilai' => $message['nipNrpAtasanPejabatPenilai'],
      'namaAtasanPejabatPenilai' => $message['namaAtasanPejabatPenilai'],
      'unitOrganisasiAtasanPejabatPenilai' => $message['unitOrganisasiAtasanPejabatPenilai'],
      'golonganAtasanPejabatPenilai' => $message['golonganAtasanPejabatPenilai'],
      'tmtGolonganAtasanPejabatPenilai' => $message['tmtGolonganAtasanPejabatPenilai'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataSkpUpdate' => $id,
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
