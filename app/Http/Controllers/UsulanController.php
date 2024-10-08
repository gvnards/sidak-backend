<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsulanController extends Controller
{
  public function getUsulan(Request $request, $idUsulanStatus = NULL, $idPegawai = NULL)
  {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $daftarPegawai = [];
    if ($idPegawai === NULL) {
      $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->header('Authorization'));
      $message = json_decode($message, true);
      $idAppRoleUser = $message['idAppRoleUser'];
      $kodeKomponenAdmin = '';
      $kdKom = DB::table('m_admin')->where('username', '=', $username)->get();
      foreach(json_decode($kdKom, true) as $key => $value) {
        $kodeKomponenAdmin = $kodeKomponenAdmin.$value['unitOrganisasi'].'%';
      }
      if ($idAppRoleUser === 1) {
        $daftarPegawai_ = DB::table('v_pegawai')->get(['id']);
      } else {
        $daftarPegawai_ = DB::table('v_pegawai')->where([
          ['kodeKomponen', 'LIKE', $kodeKomponenAdmin]
        ])->get(['id']);
      }
      foreach (json_decode($daftarPegawai_, true) as $key => $value) {
        array_push($daftarPegawai, $value['id']);
      }
    }
    $dataAnak = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_anak', 'm_data_pribadi.idPegawai', '=', 'm_data_anak.idPegawai')->join('m_usulan', 'm_data_anak.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_anak.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Anak' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_anak.id as id, m_data_anak.created_at as createdAt, m_data_anak.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataDiklat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_diklat', 'm_data_pribadi.idPegawai', '=', 'm_data_diklat.idPegawai')->join('m_usulan', 'm_data_diklat.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_diklat.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Diklat' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_diklat.id as id, m_data_diklat.created_at as createdAt, m_data_diklat.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataPangkat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pangkat', 'm_data_pribadi.idPegawai', '=', 'm_data_pangkat.idPegawai')->join('m_usulan', 'm_data_pangkat.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_pangkat.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Pangkat' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_pangkat.id as id, m_data_pangkat.created_at as createdAt, m_data_pangkat.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataPasangan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pasangan', 'm_data_pribadi.idPegawai', '=', 'm_data_pasangan.idPegawai')->join('m_usulan', 'm_data_pasangan.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_pasangan.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Pasangan' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_pasangan.id as id, m_data_pasangan.created_at as createdAt, m_data_pasangan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataPendidikan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pendidikan', 'm_data_pribadi.idPegawai', '=', 'm_data_pendidikan.idPegawai')->join('m_usulan', 'm_data_pendidikan.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_pendidikan.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Pendidikan' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_pendidikan.id as id, m_data_pendidikan.created_at as createdAt, m_data_pendidikan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataJabatan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_jabatan', 'm_data_pribadi.idPegawai', '=', 'm_data_jabatan.idPegawai')->join('m_usulan', 'm_data_jabatan.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_jabatan.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Jabatan' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_jabatan.id as id, m_data_jabatan.created_at as createdAt, m_data_jabatan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataSkp = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_skp', 'm_data_pribadi.idPegawai', '=', 'm_data_skp.idPegawai')->join('m_usulan', 'm_data_skp.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_skp.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data SKP' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_skp.id as id, m_data_skp.created_at as createdAt, m_data_skp.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataPenghargaan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_penghargaan', 'm_data_pribadi.idPegawai', '=', 'm_data_penghargaan.idPegawai')->join('m_usulan', 'm_data_penghargaan.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_penghargaan.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Penghargaan' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_penghargaan.id as id, m_data_penghargaan.created_at as createdAt, m_data_penghargaan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"));
    $dataAngkaKredit = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_angka_kredit', 'm_data_pribadi.idPegawai', '=', 'm_data_angka_kredit.idPegawai')->join('m_usulan', 'm_data_angka_kredit.idUsulan', '=', 'm_usulan.id')->where([
      ['m_data_angka_kredit.idUsulanStatus', '=', $idUsulanStatus]
    ])->select(DB::raw("m_pegawai.nip as nip, 'Data Angka Kredit' as usulanKriteria, m_data_pribadi.idPegawai as idPegawai, m_data_pribadi.nama as nama, m_data_angka_kredit.id as id, m_data_angka_kredit.created_at as createdAt, m_data_angka_kredit.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_usulan.id as idUsulan"))->union($dataAnak)->union($dataDiklat)->union($dataPangkat)->union($dataPasangan)->union($dataPendidikan)->union($dataJabatan)->union($dataSkp)->union($dataPenghargaan);

    /// Get All Data After Binding On UNION Method
    $allData = json_decode(DB::table($dataAngkaKredit)->whereIn('idPegawai', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->orderBy('createdAt', intval($idUsulanStatus) === 3 || intval($idUsulanStatus) === 4 ? 'desc' : 'asc')->get(), true);
    $callback = [
      'message' => $allData,
      'status' => 2
    ];
    return $callback;
  }

  public function getUsulanDetail($idUsulan, $usulanKriteria, Request $request)
  {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    switch ($usulanKriteria) {
      case 'Data Anak':
        $data = json_decode(DB::table('m_data_anak')->join('m_data_pasangan', 'm_data_anak.idOrangTua', '=', 'm_data_pasangan.id')->join('m_status_anak', 'm_data_anak.idStatusAnak', '=', 'm_status_anak.id')->where([
          ['m_data_anak.id', '=', $idUsulan]
        ])->get([
          'm_data_anak.*',
          'm_data_pasangan.nama as namaOrangTua',
          'm_status_anak.nama as statusAnak'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'anak', 'pdf');
        if ($data[0]['idDataAnakUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_anak')->join('m_data_pasangan', 'm_data_anak.idOrangTua', '=', 'm_data_pasangan.id')->join('m_status_anak', 'm_data_anak.idStatusAnak', '=', 'm_status_anak.id')->where([
            ['m_data_anak.id', '=', $data[0]['idDataAnakUpdate']]
          ])->get([
            'm_data_anak.*',
            'm_data_pasangan.nama as namaOrangTua',
            'm_status_anak.nama as statusAnak'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'anak', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Pasangan':
        $data = json_decode(DB::table('m_data_pasangan')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->where([
          ['m_data_pasangan.id', '=', $idUsulan]
        ])->get([
          'm_data_pasangan.*',
          'm_status_perkawinan.nama as statusPerkawinan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'pasangan', 'pdf');
        if ($data[0]['idDataPasanganUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_pasangan')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->where([
            ['m_data_pasangan.id', '=', $data[0]['idDataPasanganUpdate']]
          ])->get([
            'm_data_pasangan.*',
            'm_status_perkawinan.nama as statusPerkawinan'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'pasangan', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Pendidikan':
        $data = json_decode(DB::table('m_data_pendidikan')->join('m_jenis_pendidikan', 'm_data_pendidikan.idJenisPendidikan', '=', 'm_jenis_pendidikan.id')->join('m_tingkat_pendidikan', 'm_data_pendidikan.idTingkatPendidikan', '=', 'm_tingkat_pendidikan.id')->join('m_daftar_pendidikan', 'm_data_pendidikan.idDaftarPendidikan', '=', 'm_daftar_pendidikan.id')->where([
          ['m_data_pendidikan.id', '=', $idUsulan]
        ])->get([
          'm_data_pendidikan.*',
          'm_jenis_pendidikan.nama as jenisPendidikan',
          'm_tingkat_pendidikan.nama as tingkatPendidikan',
          'm_daftar_pendidikan.nama as pendidikan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'pendidikan', 'pdf');
        $data[0]['dokumenTranskrip'] = $this->getBlobDokumen($data[0]['idDokumenTranskrip'], $data[0]['idDokumenTranskrip'] == 1 || $data[0]['idDokumenTranskrip'] == null ? '' :  'pendidikan', 'pdf');
        if ($data[0]['idDataPendidikanUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_pendidikan')->join('m_jenis_pendidikan', 'm_data_pendidikan.idJenisPendidikan', '=', 'm_jenis_pendidikan.id')->join('m_tingkat_pendidikan', 'm_data_pendidikan.idTingkatPendidikan', '=', 'm_tingkat_pendidikan.id')->join('m_daftar_pendidikan', 'm_data_pendidikan.idDaftarPendidikan', '=', 'm_daftar_pendidikan.id')->where([
            ['m_data_pendidikan.id', '=', $data[0]['idDataPendidikanUpdate']]
          ])->get([
            'm_data_pendidikan.*',
            'm_jenis_pendidikan.nama as jenisPendidikan',
            'm_tingkat_pendidikan.nama as tingkatPendidikan',
            'm_daftar_pendidikan.nama as pendidikan'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'pendidikan', 'pdf');
          $dataBeforeUpdate[0]['dokumenTranskrip'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumenTranskrip'], $dataBeforeUpdate[0]['idDokumenTranskrip'] == 1 || $dataBeforeUpdate[0]['idDokumenTranskrip'] == null ? '' :  'pendidikan', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Jabatan':
        $unitOrganisasi = [];
        $kodeKomponen = [];
        $data_ = DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->leftJoin('m_jabatan_tugas_tambahan', 'm_data_jabatan.idJabatanTugasTambahan', '=', 'm_jabatan_tugas_tambahan.id')->where([
          ['m_data_jabatan.id', '=', $idUsulan]
        ])->get([
          'm_data_jabatan.*',
          'm_jabatan.kodeKomponen as kodeKomponen',
          'm_jabatan.nama as jabatan',
          'm_jabatan_tugas_tambahan.nama as tugasTambahan'
        ]);
        foreach(json_decode($data_, true) as $key => $value) {
          $kodeKomponen = explode(".",$value['kodeKomponen']);
        }
        for($i=0; $i<count($kodeKomponen); $i++) {
          if($kodeKomponen[$i] != '431') {
            array_push($unitOrganisasi, join(".", array_slice($kodeKomponen, 0, $i+1)));
          }
        }
        $unitOrganisasi = DB::table('m_unit_organisasi')->whereIn('kodeKomponen', $unitOrganisasi)->get('nama as unitOrganisasi');
        $data = [];
        foreach(json_decode($data_, true) as $key => $value) {
          $val = $value;
          $val['unitOrganisasi'] = $unitOrganisasi;
          array_push($data, $val);
        }
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' : 'jabatan', 'pdf');
        if ($data[0]['idDataJabatanUpdate'] !== null) {
          $unitOrganisasi = [];
          $kodeKomponen = [];
          $data_ = DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->leftJoin('m_jabatan_tugas_tambahan', 'm_data_jabatan.idJabatanTugasTambahan', '=', 'm_jabatan_tugas_tambahan.id')->where([
            ['m_data_jabatan.id', '=', $data[0]['idDataJabatanUpdate']]
          ])->get([
            'm_data_jabatan.*',
            'm_jabatan.kodeKomponen as kodeKomponen',
            'm_jabatan.nama as jabatan',
            'm_jabatan_tugas_tambahan.nama as tugasTambahan'
          ]);
          foreach(json_decode($data_, true) as $key => $value) {
            $kodeKomponen = explode(".",$value['kodeKomponen']);
          }
          for($i=0; $i<count($kodeKomponen); $i++) {
            if($kodeKomponen[$i] != '431') {
              array_push($unitOrganisasi, join(".", array_slice($kodeKomponen, 0, $i+1)));
            }
          }
          $unitOrganisasi = DB::table('m_unit_organisasi')->whereIn('kodeKomponen', $unitOrganisasi)->get('nama as unitOrganisasi');
          $dataBeforeUpdate = [];
          foreach(json_decode($data_, true) as $key => $value) {
            $val = $value;
            $val['unitOrganisasi'] = $unitOrganisasi;
            array_push($dataBeforeUpdate, $val);
          }
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' : 'jabatan', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Pangkat':
        $data = json_decode(DB::table('m_data_pangkat')->join('m_jenis_pangkat', 'm_data_pangkat.idJenisPangkat', '=', 'm_jenis_pangkat.id')->join('m_daftar_pangkat', 'm_data_pangkat.idDaftarPangkat', '=', 'm_daftar_pangkat.id')->where([
          ['m_data_pangkat.id', '=', $idUsulan]
        ])->get([
          'm_data_pangkat.*',
          'm_jenis_pangkat.nama as jenisPangkat',
          'm_daftar_pangkat.golongan as golongan',
          'm_daftar_pangkat.pangkat as pangkat'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' : 'pangkat', 'pdf');
        if ($data[0]['idDataPangkatUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_pangkat')->join('m_jenis_pangkat', 'm_data_pangkat.idJenisPangkat', '=', 'm_jenis_pangkat.id')->join('m_daftar_pangkat', 'm_data_pangkat.idDaftarPangkat', '=', 'm_daftar_pangkat.id')->where([
            ['m_data_pangkat.id', '=', $data[0]['idDataPangkatUpdate']]
          ])->get([
            'm_data_pangkat.*',
            'm_jenis_pangkat.nama as jenisPangkat',
            'm_daftar_pangkat.golongan as golongan',
            'm_daftar_pangkat.pangkat as pangkat'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' : 'pangkat', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Diklat':
        $data = json_decode(DB::table('m_data_diklat')->join('m_jenis_diklat', 'm_data_diklat.idJenisDiklat', '=', 'm_jenis_diklat.id')->join('m_daftar_diklat', 'm_data_diklat.idDaftarDiklat', '=', 'm_daftar_diklat.id')->join('m_daftar_instansi_diklat', 'm_data_diklat.idDaftarInstansiDiklat', '=', 'm_daftar_instansi_diklat.id')->where([
          ['m_data_diklat.id', '=', $idUsulan]
        ])->get([
          'm_data_diklat.*',
          'm_daftar_instansi_diklat.nama as instansi',
          'm_jenis_diklat.nama as jenisDiklat',
          'm_daftar_diklat.nama as diklat',
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' : 'diklat', 'pdf');
        if ($data[0]['idDataDiklatUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_diklat')->join('m_jenis_diklat', 'm_data_diklat.idJenisDiklat', '=', 'm_jenis_diklat.id')->join('m_daftar_diklat', 'm_data_diklat.idDaftarDiklat', '=', 'm_daftar_diklat.id')->join('m_daftar_instansi_diklat', 'm_data_diklat.idDaftarInstansiDiklat', '=', 'm_daftar_instansi_diklat.id')->where([
            ['m_data_diklat.id', '=', $data[0]['idDataDiklatUpdate']]
          ])->get([
            'm_data_diklat.*',
            'm_daftar_instansi_diklat.nama as instansi',
            'm_jenis_diklat.nama as jenisDiklat',
            'm_daftar_diklat.nama as diklat',
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'diklat', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data SKP':
        $data = json_decode(DB::table('m_data_skp')->join('m_jenis_jabatan', 'm_data_skp.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_jenis_peraturan_kinerja', 'm_data_skp.idJenisPeraturanKinerja', '=', 'm_jenis_peraturan_kinerja.id')->join('m_status_pejabat_atasan_penilai as status_pejabat_penilai', 'm_data_skp.idStatusPejabatPenilai', '=', 'status_pejabat_penilai.id')->join('m_status_pejabat_atasan_penilai as status_atasan_pejabat_penilai', 'm_data_skp.idStatusAtasanPejabatPenilai', '=', 'status_atasan_pejabat_penilai.id')->where([
          ['m_data_skp.id', '=', $idUsulan]
        ])->get([
          'm_data_skp.*',
          'm_jenis_jabatan.nama as jenisJabatan',
          'm_jenis_peraturan_kinerja.nama as peraturanKinerja',
          'status_pejabat_penilai.nama as statusPejabatPenilai',
          'status_atasan_pejabat_penilai.nama as statusAtasanPejabatPenilai'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'skp', 'pdf');
        if ($data[0]['idDataSkpUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_skp')->join('m_jenis_jabatan', 'm_data_skp.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_jenis_peraturan_kinerja', 'm_data_skp.idJenisPeraturanKinerja', '=', 'm_jenis_peraturan_kinerja.id')->join('m_status_pejabat_atasan_penilai as status_pejabat_penilai', 'm_data_skp.idStatusPejabatPenilai', '=', 'status_pejabat_penilai.id')->join('m_status_pejabat_atasan_penilai as status_atasan_pejabat_penilai', 'm_data_skp.idStatusAtasanPejabatPenilai', '=', 'status_atasan_pejabat_penilai.id')->where([
            ['m_data_skp.id', '=', $data[0]['idDataSkpUpdate']]
          ])->get([
            'm_data_skp.*',
            'm_jenis_jabatan.nama as jenisJabatan',
            'm_jenis_peraturan_kinerja.nama as peraturanKinerja',
            'status_pejabat_penilai.nama as statusPejabatPenilai',
            'status_atasan_pejabat_penilai.nama as statusAtasanPejabatPenilai'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'skp', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Penghargaan':
        $data = json_decode(DB::table('m_data_penghargaan')->join('m_daftar_jenis_penghargaan', 'm_data_penghargaan.idDaftarJenisPenghargaan', '=', 'm_daftar_jenis_penghargaan.id')->where([
          ['m_data_penghargaan.id', '=', $idUsulan]
        ])->get([
          'm_data_penghargaan.*',
          'm_daftar_jenis_penghargaan.jenisPenghargaan as jenisPenghargaan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'penghargaan', 'pdf');
        if ($data[0]['idDataPenghargaanUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_penghargaan')->join('m_daftar_jenis_penghargaan', 'm_data_penghargaan.idDaftarJenisPenghargaan', '=', 'm_daftar_jenis_penghargaan.id')->where([
            ['m_data_penghargaan.id', '=', $data[0]['idDataPenghargaanUpdate']]
          ])->get([
            'm_data_penghargaan.*',
            'm_daftar_jenis_penghargaan.jenisPenghargaan as jenisPenghargaan'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'penghargaan', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      case 'Data Angka Kredit':
        $data = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
          ['m_data_angka_kredit.id', '=', $idUsulan]
        ])->get([
          'm_data_angka_kredit.*',
          'm_daftar_jenis_angka_kredit.jenisAngkaKredit as jenisAngkaKredit',
          'm_jabatan.nama as jabatan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'pak', 'pdf');
        if ($data[0]['idDataAngkaKreditUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->leftJoin('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->leftJoin('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
            ['m_data_angka_kredit.id', '=', $data[0]['idDataAngkaKreditUpdate']]
          ])->get([
            'm_data_angka_kredit.*',
            'm_daftar_jenis_angka_kredit.jenisAngkaKredit as jenisAngkaKredit',
            'm_jabatan.nama as jabatan'
          ]), true);
          $dataBeforeUpdate[0]['dokumen'] = $this->getBlobDokumen($dataBeforeUpdate[0]['idDokumen'], $dataBeforeUpdate[0]['idDokumen'] == 1 ? '' :  'pak', 'pdf');
          array_push($data, $dataBeforeUpdate[0]);
        }
        break;
      default:
        return $this->encrypt($username, json_encode([
          'message' => 'Data tidak ditemukan.',
          'status' => 3
        ]));
        break;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function updateUsulan($idUsulan, $dataMultipleVerfivication=null, Request $request)
  {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = $dataMultipleVerfivication ?? json_decode($this->decrypt($username, $request->message), true);
    switch ($message['usulanKriteria']) {
      case 'Data Anak':
        $data = (new DataAnakController)->updateDataAnak($idUsulan, $message);
        break;
      case 'Data Diklat':
        $data = (new DataDiklatController)->updateDataDiklat($idUsulan, $message);
        break;
      case 'Data Pangkat':
        $data = (new DataGolonganPangkatController)->updateDataGolPang($idUsulan, $message);
        break;
      case 'Data Pasangan':
        $data = (new DataPasanganController)->updateDataPasangan($idUsulan, $message);
        break;
      case 'Data Pendidikan':
        $data = (new DataPendidikanController)->updateDataPendidikan($idUsulan, $message);
        break;
      case 'Data Jabatan':
        $data = (new DataJabatanController)->updateDataJabatan($idUsulan, $message);
        break;
      case 'Data Penghargaan':
        $data = (new DataPenghargaanController)->updateDataPenghargaan($idUsulan, $message);
        break;
      case 'Data Angka Kredit':
        $data = (new DataAngkaKreditController)->updateDataAngkaKredit($idUsulan, $message);
        break;
      default:
        return $this->encrypt($username, json_encode([
          'message' => 'Data tidak ditemukan.',
          'status' => 3
        ]));
        break;
    }
    if ($data['status'] === 4) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
        'status' => 3
      ]));
    }
    $callback = $data;
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateUsulanMultiple(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $successCount = 0;
    foreach ($message['dataMultiple'] as $key => $value) {
      $dataEach['idUsulanStatus'] = 3;
      $dataEach['idUsulanHasil'] = intval($message['idUsulanHasil']);
      $dataEach['keteranganUsulan'] = $message['keteranganUsulan'];
      $dataEach['usulanKriteria'] = $value['usulanKriteria'];
      $this->updateUsulan(intval($value['id']), $dataEach, $request);
    }
    $callback = [
      'message' => 'Data berhasil disimpan.',
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
