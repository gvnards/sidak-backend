<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsulanController extends Controller
{
  public function getUsulan($idUsulanStatus = NULL, $idPegawai = NULL, Request $request)
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
    $allData = json_decode(DB::table($dataAngkaKredit)->whereIn('idPegawai', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->orderBy('createdAt', 'asc')->get(), true);
    $callback = [
      'message' => $allData,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
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
        $data = json_decode(DB::table('m_data_angka_kredit')->join('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
          ['m_data_angka_kredit.id', '=', $idUsulan]
        ])->get([
          'm_data_angka_kredit.*',
          'm_daftar_jenis_angka_kredit.jenisAngkaKredit as jenisAngkaKredit',
          'm_jabatan.nama as jabatan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'pak', 'pdf');
        if ($data[0]['idDataAngkaKreditUpdate'] !== null) {
          $dataBeforeUpdate = json_decode(DB::table('m_data_angka_kredit')->join('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
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
    return $this->encrypt($username, json_encode($callback));
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
        $newData = json_decode(DB::table('m_data_anak')->where('id', '=', $idUsulan)->get(), true);
        if (intval($newData[0]['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
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
        break;
      case 'Data Diklat':
        $usulan = json_decode(DB::table('m_data_diklat')->where([
          ['id', '=', $idUsulan]
        ])->get()->toJson(), true)[0];
        if (intval($usulan['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        if (intval($usulan['idUsulan']) == 1 && $message['idUsulanHasil'] == 1) {
          $response = (new ApiSiasnSyncController)->insertRiwayatDiklatKursus($request, $idUsulan);
          if (!$response['success']) {
            $callback = [
              'message' => $response['message'],
              'status' => 3
            ];
            return $this->encrypt($username, json_encode($callback));
          } else {
            DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
              'idBkn' => $response['mapData']['rwDiklatId'] ?? $response['mapData']['rwKursusId'],
            ]);
            $dokumen = json_decode(DB::table('m_dokumen')->where([
              ['id', '=', $usulan['idDokumen']]
            ])->get()->toJson(), true)[0];
            (new ApiSiasnController)->insertDokumenRiwayat($request, $response['mapData']['rwDiklatId'] ?? $response['mapData']['rwKursusId'], $usulan['idJenisDiklat'] == 1 ? 874 : 881, 'diklat', $dokumen['nama'], 'pdf');
          }
        }
        $newData = json_decode(DB::table('m_data_diklat')->where('id', '=', $idUsulan)->get(), true);
        $idUpdate = $newData[0]['idDataDiklatUpdate'];
        $data = DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        if ($idUpdate != null) {
          if (intval($message['idUsulanHasil']) == 1) {
            $oldData = json_decode(DB::table('m_data_diklat')->where('id', '=', $idUpdate)->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_diklat')->where('id', '=', $idUpdate)->update([
                'idJenisDiklat' => $value['idJenisDiklat'],
                'idDaftarDiklat' => $value['idDaftarDiklat'],
                'namaDiklat' => $value['namaDiklat'],
                'lamaDiklat' => $value['lamaDiklat'],
                'tanggalDiklat' => $value['tanggalDiklat'],
                'idDaftarInstansiDiklat' => $value['idDaftarInstansiDiklat'],
                'institusiPenyelenggara' => $value['institusiPenyelenggara'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
              ]);
            }
            DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'diklat', 'pdf');
            }
          } else {
            $getData = $newData[0];
            DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'diklat', 'pdf');
          }
        }
        break;
      case 'Data Pangkat':
        $newData = json_decode(DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->get(), true);
        if (intval($newData[0]['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        $idUpdate = $newData[0]['idDataPangkatUpdate'];
        $data = DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        if ($idUpdate != null) {
          if (intval($message['idUsulanHasil']) == 1) {
            $oldData = json_decode(DB::table('m_data_pangkat')->where('id', '=', $idUpdate)->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_pangkat')->where('id', '=', $idUpdate)->update([
                'idJenisPangkat' => $value['idJenisPangkat'],
                'idDaftarPangkat' => $value['idDaftarPangkat'],
                'masaKerjaTahun' => $value['masaKerjaTahun'],
                'masaKerjaBulan' => $value['masaKerjaBulan'],
                'nomorDokumen' => $value['nomorDokumen'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'tmt' => $value['tmt'],
                'nomorBkn' => $value['nomorBkn'],
                'tanggalBkn' => $value['tanggalBkn'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
              ]);
            }
            DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'pangkat', 'pdf');
            }
          } else {
            $getData = $newData[0];
            DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'pangkat', 'pdf');
          }
        }
        break;
      case 'Data Pasangan':
        $newData = json_decode(DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->get(), true);
        if (intval($newData[0]['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
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
        break;
      case 'Data Pendidikan':
        $newData = json_decode(DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->get(), true);
        if (intval($newData[0]['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
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
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'pendidikan', 'pdf');
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
        break;
      case 'Data SKP':
        $newData = json_decode(DB::table('m_data_skp')->where('id', '=', $idUsulan)->get(), true);
        if (intval($newData[0]['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        $idUpdate = $newData[0]['idDataSkpUpdate'];
        $data = DB::table('m_data_skp')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        if ($idUpdate != null) {
          if (intval($message['idUsulanHasil']) == 1) {
            $oldData = json_decode(DB::table('m_data_skp')->where('id', '=', $idUpdate)->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_skp')->where('id', '=', $idUpdate)->update([
                'idJenisJabatan' => $value['idJenisJabatan'],
                'tahun' => $value['tahun'],
                'idJenisPeraturanKinerja' => $value['idJenisPeraturanKinerja'],
                'nilaiSkp' => $value['nilaiSkp'],
                'orientasiPelayanan' => $value['orientasiPelayanan'],
                'integritas' => $value['integritas'],
                'komitmen' => $value['komitmen'],
                'disiplin' => $value['disiplin'],
                'kerjaSama' => $value['kerjaSama'],
                'kepemimpinan' => $value['kepemimpinan'],
                'nilaiPrestasiKerja' => $value['nilaiPrestasiKerja'],
                'nilaiKonversi' => $value['nilaiKonversi'],
                'nilaiIntegrasi' => $value['nilaiIntegrasi'],
                'idStatusPejabatPenilai' => $value['idStatusPejabatPenilai'],
                'nipNrpPejabatPenilai' => $value['nipNrpPejabatPenilai'],
                'namaPejabatPenilai' => $value['namaPejabatPenilai'],
                'jabatanPejabatPenilai' => $value['jabatanPejabatPenilai'],
                'unitOrganisasiPejabatPenilai' => $value['unitOrganisasiPejabatPenilai'],
                'golonganPejabatPenilai' => $value['golonganPejabatPenilai'],
                'tmtGolonganPejabatPenilai' => $value['tmtGolonganPejabatPenilai'],
                'idStatusAtasanPejabatPenilai' => $value['idStatusAtasanPejabatPenilai'],
                'nipNrpAtasanPejabatPenilai' => $value['nipNrpAtasanPejabatPenilai'],
                'namaAtasanPejabatPenilai' => $value['namaAtasanPejabatPenilai'],
                'jabatanAtasanPejabatPenilai' => $value['jabatanAtasanPejabatPenilai'],
                'unitOrganisasiAtasanPejabatPenilai' => $value['unitOrganisasiAtasanPejabatPenilai'],
                'golonganAtasanPejabatPenilai' => $value['golonganAtasanPejabatPenilai'],
                'tmtGolonganAtasanPejabatPenilai' => $value['tmtGolonganAtasanPejabatPenilai'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
              ]);
            }
            DB::table('m_data_skp')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'skp', 'pdf');
            }
          } else {
            $getData = $newData[0];
            DB::table('m_data_skp')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'skp', 'pdf');
          }
        }
        break;
      case 'Data Jabatan':
        $usulan = json_decode(DB::table('m_data_jabatan')->where([
          ['id', '=', $idUsulan]
        ])->get()->toJson(), true)[0];
        if (intval($usulan['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        if (intval($usulan['idUsulan']) == 1 && intval($message['idUsulanHasil']) == 1) {
          $response = (new ApiSiasnSyncController)->insertRiwayatJabatan($request, $idUsulan);
          if (!$response['success']) {
            $callback = [
              'message' => $response['message'],
              'status' => 3
            ];
            return $this->encrypt($username, json_encode($callback));
          } else {
            DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
              'idBkn' => $response['mapData']['rwJabatanId'],
            ]);
            $dokumen = json_decode(DB::table('m_dokumen')->where([
              ['id', '=', $usulan['idDokumen']]
            ])->get()->toJson(), true)[0];
            (new ApiSiasnController)->insertDokumenRiwayat($request, $response['mapData']['rwJabatanId'], 872, 'jabatan', $dokumen['nama'], 'pdf');
          }
        } else if (intval($usulan['idUsulan']) === 2 && intval($message['idUsulanHasil']) == 1) {
          $checkData = json_decode(DB::table('m_data_jabatan')->where([
            ['id', '=', $idUsulan]
          ])->get(), true);
          $checkData = json_decode(DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
            ['m_data_jabatan.id', '=', $checkData[0]['idDataJabatanUpdate']]
          ])->get(['m_jabatan.*']), true);
          // if (str_contains($checkData[0]['kodeKomponen'], '-')) {
            $response = (new ApiSiasnSyncController)->insertRiwayatJabatan($request, $idUsulan);
          // }
        }
        $newData = json_decode(DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->get(), true);
        $idUpdate = $newData[0]['idDataJabatanUpdate'];
        $data = DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        if ($idUpdate != null) {
          if (intval($message['idUsulanHasil']) == 1) {
            $oldData = json_decode(DB::table('m_data_jabatan')->where('id', '=', $idUpdate)->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_jabatan')->where('id', '=', $idUpdate)->update([
                'idJabatan' => $value['idJabatan'],
                'isPltPlh' => $value['isPltPlh'],
                'tmt' => $value['tmt'],
                'spmt' => $value['spmt'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'nomorDokumen' => $value['nomorDokumen'],
                'idJabatanTugasTambahan' => $value['idJabatanTugasTambahan'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
              ]);
            }
            DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'jabatan', 'pdf');
            }
          } else {
            $getData = $newData[0];
            DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'jabatan', 'pdf');
          }
        }
        break;
      case 'Data Penghargaan':
        $usulan = json_decode(DB::table('m_data_penghargaan')->where([
          ['id', '=', $idUsulan]
        ])->get()->toJson(), true)[0];
        if (intval($usulan['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        if (intval($usulan['idUsulan']) == 1 && intval($message['idUsulanHasil']) == 1) {
          $response = (new ApiSiasnSyncController)->insertRiwayatPenghargaan($request, $idUsulan);
          if (!$response['success']) {
            $callback = [
              'message' => $response['message'],
              'status' => 3
            ];
            return $this->encrypt($username, json_encode($callback));
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
        break;
      case 'Data Angka Kredit':
        $usulan = json_decode(DB::table('m_data_angka_kredit')->where([
          ['id', '=', $idUsulan]
        ])->get()->toJson(), true)[0];
        if (intval($usulan['idUsulanStatus']) !== 1) {
          return $this->encrypt($username, json_encode([
            'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
            'status' => 3
          ]));
        }
        if (intval($usulan['idUsulan']) == 1 && intval($message['idUsulanHasil']) == 1) {
          $response = $response = (new ApiSiasnSyncController)->insertRiwayatAngkaKredit($request, $idUsulan);
          if (!$response['success']) {
            $callback = [
              'message' => $response['message'],
              'status' => 3
            ];
            return $this->encrypt($username, json_encode($callback));
          } else {
            DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
              'idBkn' => $response['mapData']['rwAngkaKreditId'],
            ]);
            $dokumen = json_decode(DB::table('m_dokumen')->where([
              ['id', '=', $usulan['idDokumen']]
            ])->get()->toJson(), true)[0];
            (new ApiSiasnController)->insertDokumenRiwayat($request, $response['mapData']['rwAngkaKreditId'], 879, 'pak', $dokumen['nama'], 'pdf');
          }
        }

        $newData = json_decode(DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->get(), true);
        $idUpdate = $newData[0]['idDataAngkaKreditUpdate'];
        $data = DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        if ($idUpdate != null) {
          if (intval($message['idUsulanHasil']) == 1) {
            $tahun = $message['tahun'];
            $angkaKreditUtama = $message['angkaKreditUtama'];
            $angkaKreditPenunjang = $message['angkaKreditPenunjang'];
            switch (intval($message['idDaftarJenisAngkaKredit'])) {
              case 1:
                $tahun = null;
                break;
              case 2:
                $tahun = null;
                $angkaKreditUtama = null;
                $angkaKreditPenunjang = null;
                break;
              case 3:
                $angkaKreditUtama = null;
                $angkaKreditPenunjang = null;
                break;
              default:
                break;
            }
            $oldData = json_decode(DB::table('m_data_angka_kredit')->where('id', '=', $idUpdate)->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_angka_kredit')->where('id', '=', $idUpdate)->update([
                'idDaftarJenisAngkaKredit' => $value['idDaftarJenisAngkaKredit'],
                'idDataJabatan' => $value['idDataJabatan'],
                'tahun' => $tahun,
                'periodePenilaianMulai' => $value['periodePenilaianMulai'],
                'periodePenilaianSelesai' => $value['periodePenilaianSelesai'],
                'angkaKreditUtama' => $angkaKreditUtama,
                'angkaKreditPenunjang' => $angkaKreditPenunjang,
                'angkaKreditTotal' => $value['angkaKreditTotal'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'nomorDokumen' => $value['nomorDokumen'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
              ]);
            }
            DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            if ($oldData['idDokumen'] !== null) {
              $this->deleteDokumen($oldData['idDokumen'], 'penghargaan', 'pdf');
            }
          } else {
            $getData = $newData[0];
            DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'penghargaan', 'pdf');
          }
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
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
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
