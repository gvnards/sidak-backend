<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestApiToAppSlksController extends RestApiController
{
  public function restGet(Request $request, $nipBaru) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $data = json_decode(DB::table('v_pegawai')->leftJoin('m_data_cpns_pns', 'v_pegawai.id', '=', 'm_data_cpns_pns.idPegawai')->leftJoin('m_data_pribadi', 'v_pegawai.id', '=', 'm_data_pribadi.idPegawai')->leftJoin('m_dokumen as dokumenCpns', 'm_data_cpns_pns.idDokumenSkCpns', '=', 'dokumenCpns.id')->leftJoin('m_dokumen as dokumenPns', 'm_data_cpns_pns.idDokumenSkPns', '=', 'dokumenPns.id')->where([
      ['nip', '=', $nipBaru]
    ])->get([
      'v_pegawai.id as id',
      'v_pegawai.nip as nip',
      'v_pegawai.idAsnBkn as id_pns',
      'v_pegawai.nama as nama_ekin',
      'v_pegawai.nama as nama_asn',
      'v_pegawai.gelarDepan as glr_dpn',
      'v_pegawai.gelarBelakang as glr_blk',
      'v_pegawai.tingkatPendidikan as tingkat_pendidikan_nama',
      'v_pegawai.namaSekolah as pendidikan_nama',
      'v_pegawai.tmtGolongan as tmt_gol',
      'v_pegawai.golongan as nm_gol',
      'v_pegawai.statusKepegawaian as jenis_pns',
      'v_pegawai.statusKepegawaian as status_pegawai',
      'v_pegawai.jabatan as nama_jabatan',
      'v_pegawai.jenisJabatan as jenis_jabatan',
      'v_pegawai.kodeKomponen as kodeKomponen',
      'm_data_cpns_pns.tmtCpns as tahun_aktif',
      'dokumenCpns.nama as dokumen_cpns_url',
      'dokumenPns.nama as dokumen_pns_url',
      'm_data_pribadi.alamat as alamat',
      'm_data_pribadi.tanggalLahir as tgl_lahir',
      'm_data_pribadi.tempatLahir as tempat_lahir',
    ]), true);
    if (count($data) > 0) $opd = $data[0]['kodeKomponen'];
    for($i = 0; $i < count($data); $i++) {
      $pasangan = json_decode(DB::table('m_data_pasangan')->leftJoin('m_dokumen', 'm_data_pasangan.idDokumen', '=', 'm_dokumen.id')->where([
        ['idPegawai', '=', $data[$i]['id']],
        ['idUsulanStatus', '=', 3],
        ['idUsulanHasil', '=', 1]
      ])->orderBy('tanggalStatusPerkawinan', 'desc')->get([
        'm_dokumen.nama as dokumen_nikah_url'
      ]), true);
      count($pasangan) > 0 ? $data[$i]['dokumen_nikah_url'] = $pasangan[0]['dokumen_nikah_url'] : $data[$i]['dokumen_nikah_url'] = null;
      $data[$i]['agama'] = '';
      $data[$i]['jenis_kelamin'] = intval($data[$i]['nip'][14]) === 1 ? 'Laki-laki' : 'Perempuan';
      while (!str_contains($opd, '-')) {
        $checkOpd = json_decode(DB::table('m_unit_organisasi')->where([
          ['kodeKomponen', '=', $opd]
        ])->get(), true);
        $data[0]['nama_opd'] = '';
        if ($checkOpd[0]['kodeKomponen'] === '431.000' || $checkOpd[0]['kodeKomponen'] === '431.100' || $checkOpd[0]['kodeKomponen'] === '431.200' || $checkOpd[0]['kodeKomponen'] === '431.300.301' || $checkOpd[0]['kodeKomponen'] === '431.300.302' || $checkOpd[0]['kodeKomponen'] === '431.300.303' || $checkOpd[0]['kodeKomponen'] === '431.300.304' || $checkOpd[0]['kodeKomponen'] === '431.300.305' || $checkOpd[0]['kodeKomponen'] === '431.300.306' || $checkOpd[0]['kodeKomponen'] === '431.300.307' || $checkOpd[0]['kodeKomponen'] === '431.300.308' || $checkOpd[0]['kodeKomponen'] === '431.300.309' || $checkOpd[0]['kodeKomponen'] === '431.300.310' || $checkOpd[0]['kodeKomponen'] === '431.300.311' || $checkOpd[0]['kodeKomponen'] === '431.300.312' || $checkOpd[0]['kodeKomponen'] === '431.300.313' || $checkOpd[0]['kodeKomponen'] === '431.300.314' || $checkOpd[0]['kodeKomponen'] === '431.300.315' || $checkOpd[0]['kodeKomponen'] === '431.300.316' || $checkOpd[0]['kodeKomponen'] === '431.300.317' || $checkOpd[0]['kodeKomponen'] === '431.300.318' || $checkOpd[0]['kodeKomponen'] === '431.400.401' || $checkOpd[0]['kodeKomponen'] === '431.400.402' || $checkOpd[0]['kodeKomponen'] === '431.400.403' || $checkOpd[0]['kodeKomponen'] === '431.400.404' || $checkOpd[0]['kodeKomponen'] === '431.400.405' || $checkOpd[0]['kodeKomponen'] === '431.400.406' || $checkOpd[0]['kodeKomponen'] === '431.500.501' || $checkOpd[0]['kodeKomponen'] === '431.500.502' || $checkOpd[0]['kodeKomponen'] === '431.500.503' || $checkOpd[0]['kodeKomponen'] === '431.500.504' || $checkOpd[0]['kodeKomponen'] === '431.500.505' || $checkOpd[0]['kodeKomponen'] === '431.500.506' || $checkOpd[0]['kodeKomponen'] === '431.500.507' || $checkOpd[0]['kodeKomponen'] === '431.500.508' || $checkOpd[0]['kodeKomponen'] === '431.500.509' || $checkOpd[0]['kodeKomponen'] === '431.500.510' || $checkOpd[0]['kodeKomponen'] === '431.500.511' || $checkOpd[0]['kodeKomponen'] === '431.500.512' || $checkOpd[0]['kodeKomponen'] === '431.500.513' || $checkOpd[0]['kodeKomponen'] === '431.500.514' || $checkOpd[0]['kodeKomponen'] === '431.500.515' || $checkOpd[0]['kodeKomponen'] === '431.500.516' || $checkOpd[0]['kodeKomponen'] === '431.500.517' || $checkOpd[0]['kodeKomponen'] === '431.500.508.10.1' || $checkOpd[0]['kodeKomponen'] === '431.500.508.10.2' || $checkOpd[0]['kodeKomponen'] === '431.500.507.10.1' || $checkOpd[0]['kodeKomponen'] === '431.500.507.10.2' || str_contains($checkOpd[0]['nama'], 'SD') || str_contains($checkOpd[0]['nama'], 'TK') || str_contains($checkOpd[0]['nama'], 'SMP')) {
          $data[0]['nama_opd'] = $checkOpd[0]['nama'];
          $opd = '-';
        } else {
          $opdTemp = explode('.', $opd);
          array_pop($opdTemp);
          $opd = implode('.', $opdTemp);
        }
      }
      if($data[$i]['dokumen_cpns_url'] !== null) {
        $data[$i]['dokumen_cpns_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['dokumen_cpns_url'];
      }
      if($data[$i]['dokumen_pns_url'] !== null) {
        $data[$i]['dokumen_pns_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['dokumen_pns_url'];
      }
      if($data[$i]['dokumen_nikah_url'] !== null) {
        $data[$i]['dokumen_nikah_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['dokumen_nikah_url'];
      }
    }
    if (count($data) > 0) unset($data[0]['kodeKomponen']);
    return $data;
  }
}

/// catatan: itu tadi yg pertama ya, nah yg ke dua ini itu, aslinya sama, cuman, ketika menyebutkan Seksi/Bidang/Sub Bagian, itu lgsg ke OPD, tapi kalo masuk ke PKM/SD/SMP/dsb, kalo ambil OPD nya, nanti numpuknya pasti di Dinkes-nya/Dispendikbud-nya (CONFIRMED)