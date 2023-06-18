<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    $data = [];
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
    $dataAnak = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_anak', 'm_pegawai.id', '=', 'm_data_anak.idPegawai')->join('m_usulan', 'm_data_anak.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_anak.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_anak.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_anak.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_anak.id as id',
      'm_data_anak.created_at as createdAt',
      'm_data_anak.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataAnak, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Anak';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataDiklat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_diklat', 'm_pegawai.id', '=', 'm_data_diklat.idPegawai')->join('m_usulan', 'm_data_diklat.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_diklat.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_diklat.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_diklat.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_diklat.id as id',
      'm_data_diklat.created_at as createdAt',
      'm_data_diklat.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataDiklat, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Diklat';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataPangkat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->join('m_usulan', 'm_data_pangkat.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_pangkat.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_pangkat.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_pangkat.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_pangkat.id as id',
      'm_data_pangkat.created_at as createdAt',
      'm_data_pangkat.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataPangkat, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Pangkat';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataPasangan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->join('m_usulan', 'm_data_pasangan.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_pasangan.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_pasangan.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_pasangan.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_pasangan.id as id',
      'm_data_pasangan.created_at as createdAt',
      'm_data_pasangan.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataPasangan, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Pasangan';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataPendidikan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->join('m_usulan', 'm_data_pendidikan.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_pendidikan.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_pendidikan.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_pendidikan.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_pendidikan.id as id',
      'm_data_pendidikan.created_at as createdAt',
      'm_data_pendidikan.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataPendidikan, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Pendidikan';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataJabatan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_usulan', 'm_data_jabatan.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_jabatan.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_jabatan.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_jabatan.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_jabatan.id as id',
      'm_data_jabatan.created_at as createdAt',
      'm_data_jabatan.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataJabatan, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data Jabatan';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    $dataSkp = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_skp', 'm_pegawai.id', '=', 'm_data_skp.idPegawai')->join('m_usulan', 'm_data_skp.idUsulan', '=', 'm_usulan.id')->join('m_usulan_status', 'm_data_skp.idUsulanStatus', '=', 'm_usulan_status.id')->join('m_usulan_hasil', 'm_data_skp.idUsulanHasil', '=', 'm_usulan_hasil.id')->whereIn('m_pegawai.id', $idPegawai !== NULL ? [$idPegawai] : $daftarPegawai)->where([
      ['m_data_skp.idUsulanStatus', '=', $idUsulanStatus]
    ])->get([
      'm_pegawai.id as idPegawai',
      'm_data_pribadi.nama as nama',
      'm_data_skp.id as id',
      'm_data_skp.created_at as createdAt',
      'm_data_skp.updated_at as updatedAt',
      'm_usulan.id as idUsulan',
      'm_usulan.nama as usulan',
      'm_usulan_status.id as idUsulanStatus',
      'm_usulan_status.nama as usulanStatus',
      'm_usulan_hasil.id as idUsulanHasil',
      'm_usulan_hasil.nama as usulanHasil'
    ]);
    foreach (json_decode($dataSkp, true) as $key => $value) {
      $val = $value;
      $val['usulanKriteria'] = 'Data SKP';
      $val['c'] = strtotime($value['createdAt']);
      array_push($data, $val);
    }
    if (count($data) != 0 && count($data) != 1) {
      for ($i = 0; $i < count($data) - 1; $i++) {
        for ($j = $i + 1; $j < count($data); $j++) {
          if ($data[$i]['c'] > $data[$j]['c']) {
            $data_ = $data[$i];
            $data[$i] = $data[$j];
            $data[$j] = $data_;
          }
        }
      }
    }
    $callback = [
      'message' => $data,
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
        break;
      case 'Data Pasangan':
        $data = json_decode(DB::table('m_data_pasangan')->join('m_status_perkawinan', 'm_data_pasangan.idStatusPerkawinan', '=', 'm_status_perkawinan.id')->where([
          ['m_data_pasangan.id', '=', $idUsulan]
        ])->get([
          'm_data_pasangan.*',
          'm_status_perkawinan.nama as statusPerkawinan'
        ]), true);
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'pasangan', 'pdf');
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
        $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], $data[0]['idDokumen'] == 1 ? '' :  'jabatan', 'pdf');
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

  public function updateUsulan($idUsulan, Request $request)
  {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    switch ($message['usulanKriteria']) {
      case 'Data Anak':
        $data = DB::table('m_data_anak')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_anak')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_anak')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_anak')->where('id', '=', $message['idUpdate'])->update([
                'nama' => $value['nama'],
                'tempatLahir' => $value['tempatLahir'],
                'tanggalLahir' => $value['tanggalLahir'],
                'nomorDokumen' => $value['nomorDokumen'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'idOrangTua' => $value['idOrangTua'],
                'idStatusAnak' => $value['idStatusAnak'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_anak')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'anak', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_anak')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
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
          }
        }
        $data = DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_diklat')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_diklat')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_diklat')->where('id', '=', $message['idUpdate'])->update([
                'idJenisDiklat' => $value['idJenisDiklat'],
                'idDaftarDiklat' => $value['idDaftarDiklat'],
                'namaDiklat' => $value['namaDiklat'],
                'lamaDiklat' => $value['lamaDiklat'],
                'tanggalDiklat' => $value['tanggalDiklat'],
                'idDaftarInstansiDiklat' => $value['idDaftarInstansiDiklat'],
                'institusiPenyelenggara' => $value['institusiPenyelenggara'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'diklat', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_diklat')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
            DB::table('m_data_diklat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'diklat', 'pdf');
          }
        }
        break;
      case 'Data Pangkat':
        $data = DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_pangkat')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_pangkat')->where('id', '=', $message['idUpdate'])->update([
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
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'pangkat', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_pangkat')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
            DB::table('m_data_pangkat')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'pangkat', 'pdf');
          }
        }
        break;
      case 'Data Pasangan':
        $data = DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_pasangan')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_pasangan')->where('id', '=', $message['idUpdate'])->update([
                'nama' => $value['nama'],
                'tempatLahir' => $value['tempatLahir'],
                'tanggalLahir' => $value['tanggalLahir'],
                'tanggalStatusPerkawinan' => $value['tanggalStatusPerkawinan'],
                'nomorDokumen' => $value['nomorDokumen'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'idStatusPerkawinan' => $value['idStatusPerkawinan'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'pasangan', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_pasangan')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
            DB::table('m_data_pasangan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'pasangan', 'pdf');
          }
        }
        break;
      case 'Data Pendidikan':
        $data = DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'idDataPendidikanUpdate' => null,
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_pendidikan')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_pendidikan')->where('id', '=', $message['idUpdate'])->update([
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
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'pendidikan', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_pendidikan')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
            DB::table('m_data_pendidikan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'pendidikan', 'pdf');
          }
        }
        break;
      case 'Data SKP':
        $data = DB::table('m_data_skp')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_skp')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_skp')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_skp')->where('id', '=', $message['idUpdate'])->update([
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
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_skp')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'skp', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_skp')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
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
        if (intval($usulan['idUsulan']) == 1 && $message['idUsulanHasil'] == 1) {
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
          }
        }
        $data = DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
          'idUsulanStatus' => $message['idUsulanStatus'],
          'idUsulanHasil' => $message['idUsulanHasil'],
          'keteranganUsulan' => $message['keteranganUsulan'],
          'idDataJabatanUpdate' => null,
        ]);
        if ($message['idUpdate'] != null) {
          if ($message['idUsulanHasil'] == 1) {
            $newData = json_decode(DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->get(), true);
            $oldData = json_decode(DB::table('m_data_jabatan')->where('id', '=', $message['idUpdate'])->get(), true)[0];
            foreach ($newData as $key => $value) {
              $data = DB::table('m_data_jabatan')->where('id', '=', $message['idUpdate'])->update([
                'idJabatan' => $value['idJabatan'],
                'isPltPlh' => $value['isPltPlh'],
                'tmt' => $value['tmt'],
                'spmt' => $value['spmt'],
                'tanggalDokumen' => $value['tanggalDokumen'],
                'nomorDokumen' => $value['nomorDokumen'],
                'idDokumen' => $value['idDokumen'],
                'updated_at' => $value['updated_at'],
              ]);
            }
            DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($oldData['idDokumen'], 'jabatan', 'pdf');
          } else {
            $getData = json_decode(DB::table('m_data_jabatan')->where([
              ['id', '=', $idUsulan]
            ])->get(), true)[0];
            DB::table('m_data_jabatan')->where('id', '=', $idUsulan)->update([
              'idDokumen' => 1
            ]);
            $this->deleteDokumen($getData['idDokumen'], 'jabatan', 'pdf');
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
}
