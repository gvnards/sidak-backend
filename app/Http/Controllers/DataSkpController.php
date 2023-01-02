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
      $data = DB::table('m_pegawai')->join('m_data_skp', 'm_pegawai.id', '=', 'm_data_skp.idPegawai')->join('m_jenis_jabatan', 'm_data_skp.idJenisJabatan', '=', 'm_jenis_jabatan.id')->leftJoin('m_dokumen', 'm_data_skp.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_skp.id', '=', $idDataSkp],
      ])->get([
        'm_data_skp.*',
        'm_dokumen.dokumen'
      ]);
    }
    $callback = [
      'message' => $data,
      'status' => 1
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
      'status' => 1
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
      'status' => 1
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
      'status' => 1
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
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    // jika dokumen sama, maka gunakan yang lama, jika tidak, insert baru
    $dokumenSearch = json_decode(DB::table('m_dokumen')->where([
      ['dokumen', '=', $message['dokumen']],
      ['nama', '=', "DOK_SKP_$nip"]
      ])->get(), true);
    if (count($dokumenSearch) === 0) {
      $dokumen = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_SKP_$nip",
        'dokumen' => $message['dokumen'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    } else {
      foreach ($dokumenSearch as $key => $value) {
        $dokumen = $value['id'];
      }
    }

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
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method." : "Data gagal diusulkan untuk $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
