<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportDataController extends Controller
{
  private function getDataUsulan($message) {
    $isBelumTerverifikasi = false;
    $isTerverifikasi = false;
    foreach ($message['statusVerifikasi'] as $status) {
      if (intval($status) === 2) $isBelumTerverifikasi = true;
      if (intval($status) === 1) $isTerverifikasi = true;
    }
    $dataAnak = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_anak', 'm_pegawai.id', '=', 'm_data_anak.idPegawai')->join('m_usulan', 'm_data_anak.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_anak.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_anak.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("2 as daftarUsulan, 'Data Anak' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_anak.id as id, m_data_anak.created_at as created_at, m_data_anak.updated_at as updated_at, m_data_anak.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_anak.idUsulan as idUsulan, m_data_anak.idUsulanStatus as idUsulanStatus"));
    $dataDiklat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_diklat', 'm_pegawai.id', '=', 'm_data_diklat.idPegawai')->join('m_usulan', 'm_data_diklat.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_diklat.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_diklat.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("8 as daftarUsulan, 'Data Diklat' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_diklat.id as id, m_data_diklat.created_at as created_at, m_data_diklat.updated_at as updated_at, m_data_diklat.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_diklat.idUsulan as idUsulan, m_data_diklat.idUsulanStatus as idUsulanStatus"));
    $dataPangkat = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->join('m_usulan', 'm_data_pangkat.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_pangkat.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_pangkat.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("5 as daftarUsulan, 'Data Pangkat' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_pangkat.id as id, m_data_pangkat.created_at as created_at, m_data_pangkat.updated_at as updated_at, m_data_pangkat.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_pangkat.idUsulan as idUsulan, m_data_pangkat.idUsulanStatus as idUsulanStatus"));
    $dataPasangan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pasangan', 'm_pegawai.id', '=', 'm_data_pasangan.idPegawai')->join('m_usulan', 'm_data_pasangan.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_pasangan.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_pasangan.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("2 as daftarUsulan, 'Data Pasangan' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_pasangan.id as id, m_data_pasangan.created_at as created_at, m_data_pasangan.updated_at as updated_at, m_data_pasangan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_pasangan.idUsulan as idUsulan, m_data_pasangan.idUsulanStatus as idUsulanStatus"));
    $dataPendidikan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->join('m_usulan', 'm_data_pendidikan.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_pendidikan.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_pendidikan.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("3 as daftarUsulan, 'Data Pendidikan' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_pendidikan.id as id, m_data_pendidikan.created_at as created_at, m_data_pendidikan.updated_at as updated_at, m_data_pendidikan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_pendidikan.idUsulan as idUsulan, m_data_pendidikan.idUsulanStatus as idUsulanStatus"));
    $dataJabatan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_usulan', 'm_data_jabatan.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_jabatan.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_jabatan.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("6 as daftarUsulan, 'Data Jabatan' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_jabatan.id as id, m_data_jabatan.created_at as created_at, m_data_jabatan.updated_at as updated_at, m_data_jabatan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_jabatan.idUsulan as idUsulan, m_data_jabatan.idUsulanStatus as idUsulanStatus"));
    $dataSkp = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_skp', 'm_pegawai.id', '=', 'm_data_skp.idPegawai')->join('m_usulan', 'm_data_skp.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_skp.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_skp.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("7 as daftarUsulan, 'Data SKP' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_skp.id as id, m_data_skp.created_at as created_at, m_data_skp.updated_at as updated_at, m_data_skp.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_skp.idUsulan as idUsulan, m_data_skp.idUsulanStatus as idUsulanStatus"));
    $dataPenghargaan = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_penghargaan', 'm_pegawai.id', '=', 'm_data_penghargaan.idPegawai')->join('m_usulan', 'm_data_penghargaan.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_penghargaan.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_penghargaan.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("11 as daftarUsulan, 'Data Penghargaan' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_penghargaan.id as id, m_data_penghargaan.created_at as created_at, m_data_penghargaan.updated_at as updated_at, m_data_penghargaan.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_penghargaan.idUsulan as idUsulan, m_data_penghargaan.idUsulanStatus as idUsulanStatus"));
    $dataAngkaKredit = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_angka_kredit', 'm_pegawai.id', '=', 'm_data_angka_kredit.idPegawai')->join('m_usulan', 'm_data_angka_kredit.idUsulan', '=', 'm_usulan.id')
    ->where(function ($query) use ($message, $isTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isTerverifikasi ? [1,2] : [])
      ->whereBetween('m_data_angka_kredit.updated_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->orWhere(function ($query) use ($message, $isBelumTerverifikasi) {
      $query->whereIn('idUsulanHasil', $isBelumTerverifikasi ? [3] : [])
      ->whereBetween('m_data_angka_kredit.created_at', [$message['startDate'].' 00:00:00', $message['endDate'].' 23:59:59']);
    })
    ->select(DB::raw("12 as daftarUsulan, 'Data Angka Kredit' as usulanKriteria, m_pegawai.nip as nip, m_data_pribadi.nama as nama, m_data_angka_kredit.id as id, m_data_angka_kredit.created_at as created_at, m_data_angka_kredit.updated_at as updated_at, m_data_angka_kredit.idUsulanHasil as idUsulanHasil, m_usulan.nama as usulan, m_data_angka_kredit.idUsulan as idUsulan, m_data_angka_kredit.idUsulanStatus as idUsulanStatus"))->union($dataAnak)->union($dataDiklat)->union($dataPangkat)->union($dataPasangan)->union($dataPendidikan)->union($dataJabatan)->union($dataSkp)->union($dataPenghargaan);

    $dataTemp = json_decode(DB::table($dataAngkaKredit)->orderBy('updated_at', 'asc')->get(), true);
    $data = [];
    foreach ($dataTemp as $dt) {
      if (array_search($dt['daftarUsulan'], $message['daftarUsulan']) !== false) {
        $status_verifikasi = "";
        switch (intval($dt['idUsulanHasil'])) {
          case 1:
            $status_verifikasi = "Terverifikasi (Diterima)";
            break;
          case 2:
            $status_verifikasi = "Terverifikasi (Ditolak)";
            break;
          default:
            $status_verifikasi = "Belum Terverifikasi";
            break;
        }
        array_push($data, [
          'nip' => "'".$dt['nip'],
          'nama' => $dt['nama'],
          'usulan' => $dt['usulan'].' '.$dt['usulanKriteria'],
          'tgl_dibuat' => $dt['created_at'],
          'tgl_diupdate' => $dt['updated_at'],
          'status_verifikasi' => $status_verifikasi
        ]);
      }
    }
    return $data;
  }
  public function exportData(Request $request, $kriteria) {

    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = [];
    if ($kriteria === 'usulan') {
      $data = $this->getDataUsulan($message);
    }
    return [
      'message' => $data,
      'status' => 2
    ];;
  }
  public function created(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if (!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $daftarExport = json_decode(DB::table('m_daftar_export')->get(), true);

    return [
      "status" => 2,
      "message" => [
        "daftarExport" => $daftarExport
      ]
    ];
  }
  public function dataUsulanCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if (!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $statusVerifikasi = json_decode(DB::table('m_usulan_hasil')->where('id', '!=', 2)->get(), true);
    foreach ($statusVerifikasi as $idx => $value) {
      if ($value['nama'] === 'Diterima') $statusVerifikasi[$idx]['nama'] = 'Terverifikasi';
      if ($value['nama'] === 'Menunggu') $statusVerifikasi[$idx]['nama'] = 'Belum Terverifikasi';
    }
    $daftarUsulan = DB::table('m_app_pegawaimenu')->whereIn('id', [2,3,5,6,7,8,9,11,12])->get();
    return [
      'status' => 2,
      'message' => [
        'statusVerifikasi' => $statusVerifikasi,
        'daftarUsulan' => $daftarUsulan,
      ]
    ];
  }
}
