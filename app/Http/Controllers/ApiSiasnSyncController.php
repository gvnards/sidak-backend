<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ApiSiasnSyncController extends ApiSiasnController
{

  public function syncDataPribadi(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $getAsn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get(), true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];
    $response = $this->getDataUtamaASN($request, $nipBaru);
    if ($response['data'] === 'Data tidak ditemukan') {
      return [
        'message' => 'Data tidak ditemukan.',
        'status' => 3
      ];
    }
    $response = $response['data'];
    DB::table('m_data_pribadi')->insert([
      'id' => NULL,
      'nama' => $response['nama'],
      'tempatLahir' => $response['tempatLahir'],
      'tanggalLahir' => date('Y-m-d', strtotime($response['tglLahir'])),
      'alamat' => $response['alamat'],
      'ktp' => $response['nik'],
      'nomorHp' => $response['noHp'],
      'email' => $response['email'],
      'npwp' => $response['noNpwp'],
      'bpjs' => $response['bpjs'],
      'idPegawai' => $idPegawai,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);

    $callback = [
      'message' => "Data Pribadi sudah berhasil disinkronisasi dari MySAPK.",
      'status' => 2
    ];
    return $callback;
  }

  public function syncDataCpnsPns(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $getAsn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get(), true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];
    $response = $this->getDataUtamaASN($request, $nipBaru);
    if ($response['data'] === 'Data tidak ditemukan') {
      return [
        'message' => 'Data tidak ditemukan.',
        'status' => 3
      ];
    }
    $response = $response['data'];
    DB::table('m_data_cpns_pns')->insert([
      'id' => NULL,
      'idPegawai' => $idPegawai,
      'tmtCpns' => $response['tmtCpns'] == null || $response['tmtCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tmtCpns'])),
      'tglSkCpns' => $response['tglSkCpns'] == null || $response['tglSkCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkCpns'])),
      'nomorSkCpns' => $response['nomorSkCpns'],
      'tglSpmt' => $response['tglSkCpns'] == null || $response['tglSkCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkCpns'])),
      'nomorSpmt' => $response['noSpmt'],
      'idPejabatPengangkatCpns' => 1,
      'idDokumenSkCpns' => NULL,
      'tmtPns' => $response['tmtPns'] == null || $response['tmtPns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tmtPns'])),
      'tglSkPns' => $response['tglSkPns'] == null || $response['tglSkPns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkPns'])),
      'nomorSkPns' => $response['nomorSkPns'],
      'tglSttpl' => $response['tglSttpl'] == null || $response['tglSttpl'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSttpl'])),
      'nomorSttpl' => $response['nomorSttpl'],
      'tglSuratDokter' => $response['tglSuratKeteranganDokter'] == null || $response['tglSuratKeteranganDokter'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSuratKeteranganDokter'])),
      'nomorSuratDokter' => $response['noSuratKeteranganDokter'],
      'nomorKarpeg' => $response['noSeriKarpeg'],
      'nomorKarisKarsu' => '',
      'idDokumenSkPns' => NULL,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);

    $callback = [
      'message' => "Data CPNS/PNS sudah berhasil disinkronisasi dari MySAPK.",
      'status' => 2
    ];
    return $callback;
  }
  public function syncAngkaKreditASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $this->syncJabatanASN($request, $idPegawai);

    $getAsn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get(), true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    $angkaKreditFromSiasn = $this->getRiwayatAngkaKreditASN($request, $nipBaru);

    if (!isset($angkaKreditFromSiasn['data'])) {
      $angkaKreditFromSiasn['data'] = [];
    } else if (gettype($angkaKreditFromSiasn['data']) != "array") {
      $angkaKreditFromSiasn['data'] = [];
    }
    $angkaKreditFromSiasn = $angkaKreditFromSiasn['data'];

    $angkaKreditFromSidak = json_decode(DB::table('m_data_angka_kredit')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get(), true);

    ///// cek apakah angka kredit dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    if (count($angkaKreditFromSiasn) > 0) {
      for($i = 0; $i < count($angkaKreditFromSidak); $i++) {
        if ($angkaKreditFromSidak[$i]['idBkn'] != '' && $angkaKreditFromSidak[$i]['idBkn'] != null) {
          $isFind = false;
          for($j=0; $j<count($angkaKreditFromSiasn); $j++) {
            if ($angkaKreditFromSidak[$i]['idBkn'] == $angkaKreditFromSiasn[$j]['id']) {
              $isFind = true;
            }
          }
          if (!$isFind && count($angkaKreditFromSiasn)) {
            DB::table('m_data_angka_kredit')->where([
              ['idDataAngkaKreditUpdate', '=', $angkaKreditFromSidak[$i]['id']]
            ])->delete();
            DB::table('m_data_angka_kredit')->where([
              ['id', '=', $angkaKreditFromSidak[$i]['id']]
            ])->delete();
          }
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newAngkaKreditFromSiasn = [];
    $updateAngkaKreditFromSiasn = [];
    for($i=0; $i<count($angkaKreditFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($angkaKreditFromSidak); $j++) {
        // cek
        if ($angkaKreditFromSiasn[$i]['id'] == $angkaKreditFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) array_push($newAngkaKreditFromSiasn, $angkaKreditFromSiasn[$i]);
      else array_push($updateAngkaKreditFromSiasn, $angkaKreditFromSiasn[$i]);
    }

    // update to database
    for($i = 0; $i < count($updateAngkaKreditFromSiasn); $i++) {
      if ($updateAngkaKreditFromSiasn[$i]['rwJabatan'] !== '' || $updateAngkaKreditFromSiasn[$i]['rwJabatan'] !== null) {
        $dataJabatanSidak = NULL;
        if ($updateAngkaKreditFromSiasn[$i]['rwJabatan'] !== '') {
          $dataJabatanSidakTemp = json_decode(DB::table('m_data_jabatan')->where([
            ['idBkn', '=', $updateAngkaKreditFromSiasn[$i]['rwJabatan']]
          ])->get(), true);
          if (count($dataJabatanSidakTemp) > 0) {
            $dataJabatanSidak = $dataJabatanSidakTemp[0]['id'];
          }
        }
        $idDaftarJenisAngkaKredit = 4;
        if ($updateAngkaKreditFromSiasn[$i]['isAngkaKreditPertama'] == '1') $idDaftarJenisAngkaKredit = 1;
        else if ($updateAngkaKreditFromSiasn[$i]['isIntegrasi'] == '1') $idDaftarJenisAngkaKredit = 2;
        else if ($updateAngkaKreditFromSiasn[$i]['isKonversi'] == '1') $idDaftarJenisAngkaKredit = 3;
        DB::table('m_data_angka_kredit')->where([
          ['idBkn', '=', $updateAngkaKreditFromSiasn[$i]['id']]
        ])->update([
          'idDaftarJenisAngkaKredit' => $idDaftarJenisAngkaKredit,
          'idDataJabatan' => $dataJabatanSidak,
          'tahun' => $idDaftarJenisAngkaKredit == 3 ? $updateAngkaKreditFromSiasn[$i]['tahunSelesaiPenailan'] : NULL,
          'periodePenilaianMulai' => date("Y-m-d", strtotime($updateAngkaKreditFromSiasn[$i]['tahunMulaiPenailan']."-".$updateAngkaKreditFromSiasn[$i]['bulanMulaiPenailan']."-01")),
          'periodePenilaianSelesai' => date("Y-m-t", strtotime($updateAngkaKreditFromSiasn[$i]['tahunSelesaiPenailan']."-".$updateAngkaKreditFromSiasn[$i]['bulanSelesaiPenailan']."-01")),
          'angkaKreditUtama' => $updateAngkaKreditFromSiasn[$i]['kreditUtamaBaru'] === '' ? NULL : $updateAngkaKreditFromSiasn[$i]['kreditUtamaBaru'],
          'angkaKreditPenunjang' => $updateAngkaKreditFromSiasn[$i]['kreditPenunjangBaru'] === '' ? NULL : $updateAngkaKreditFromSiasn[$i]['kreditPenunjangBaru'],
          'angkaKreditTotal' => $updateAngkaKreditFromSiasn[$i]['kreditBaruTotal'],
          'tanggalDokumen' => $updateAngkaKreditFromSiasn[$i]['tanggalSk'] == NULL || $updateAngkaKreditFromSiasn[$i]['tanggalSk'] == '' ? NULL : date('Y-m-d', strtotime($updateAngkaKreditFromSiasn[$i]['tanggalSk'])),
          'nomorDokumen' => $updateAngkaKreditFromSiasn[$i]['nomorSk'],
          'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }

    // insert to database
    $affected = '';
    for($i = 0; $i < count($newAngkaKreditFromSiasn); $i++) {
      if ($newAngkaKreditFromSiasn[$i]['rwJabatan'] !== '' || $newAngkaKreditFromSiasn[$i]['rwJabatan'] !== null) {
        $dataJabatanSidak = NULL;
        if ($newAngkaKreditFromSiasn[$i]['rwJabatan'] !== '') {
          $dataJabatanSidakTemp = json_decode(DB::table('m_data_jabatan')->where([
            ['idBkn', '=', $newAngkaKreditFromSiasn[$i]['rwJabatan']]
          ])->get(), true);
          if (count($dataJabatanSidakTemp) > 0) {
            $dataJabatanSidak = $dataJabatanSidakTemp[0]['id'];
          }
        }
        $idDaftarJenisAngkaKredit = 4;
        if ($newAngkaKreditFromSiasn[$i]['isAngkaKreditPertama'] == '1') $idDaftarJenisAngkaKredit = 1;
        else if ($newAngkaKreditFromSiasn[$i]['isIntegrasi'] == '1') $idDaftarJenisAngkaKredit = 2;
        else if ($newAngkaKreditFromSiasn[$i]['isKonversi'] == '1') $idDaftarJenisAngkaKredit = 3;
        DB::table('m_data_angka_kredit')->insert([
          'id' => NULL,
          'idDaftarJenisAngkaKredit' => $idDaftarJenisAngkaKredit,
          'idDataJabatan' => $dataJabatanSidak,
          'tahun' => $idDaftarJenisAngkaKredit == 3 ? $newAngkaKreditFromSiasn[$i]['tahunSelesaiPenailan'] : NULL,
          'periodePenilaianMulai' => date("Y-m-d", strtotime($newAngkaKreditFromSiasn[$i]['tahunMulaiPenailan']."-".$newAngkaKreditFromSiasn[$i]['bulanMulaiPenailan']."-01")),
          'periodePenilaianSelesai' => date("Y-m-t", strtotime($newAngkaKreditFromSiasn[$i]['tahunSelesaiPenailan']."-".$newAngkaKreditFromSiasn[$i]['bulanSelesaiPenailan']."-01")),
          'angkaKreditUtama' => $newAngkaKreditFromSiasn[$i]['kreditUtamaBaru'] === '' ? NULL : $newAngkaKreditFromSiasn[$i]['kreditUtamaBaru'],
          'angkaKreditPenunjang' => $newAngkaKreditFromSiasn[$i]['kreditPenunjangBaru'] === '' ? NULL : $newAngkaKreditFromSiasn[$i]['kreditPenunjangBaru'],
          'angkaKreditTotal' => $newAngkaKreditFromSiasn[$i]['kreditBaruTotal'],
          'tanggalDokumen' => $newAngkaKreditFromSiasn[$i]['tanggalSk'] == NULL || $newAngkaKreditFromSiasn[$i]['tanggalSk'] == '' ? NULL : date('Y-m-d', strtotime($newAngkaKreditFromSiasn[$i]['tanggalSk'])),
          'nomorDokumen' => $newAngkaKreditFromSiasn[$i]['nomorSk'],
          'idDokumen' => NULL,
          'idPegawai' => intval($idPegawai),
          'idUsulan' => 1,
          'idUsulanStatus' => 4,
          'idUsulanHasil' => 1,
          'idDataAngkaKreditUpdate' => NULL,
          'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'idBkn' => $newAngkaKreditFromSiasn[$i]['id'],
        ]);
      }
    }

    $getDataAngkaKredit = (new DataAngkaKreditController)->getListDataAngkaKredit($request, $idPegawai);

    $callback = [
      'message' => "Data angka kredit sudah berhasil disinkronisasi dari MySAPK.\nJika setelah sinkronisasi tidak ada angka kredit yang muncul, silahkan tambahkan angka kredit sesuai dengan dasar Sertifikat yang dimiliki.\n Dan jika terdapat ketidaksesuaian angka kredit, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataAngkaKredit['message']['dataAngkaKredit'],
      'status' => 2
    ];
    return $callback;
  }
  public function syncJabatanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $jabatanFromSiasn = $this->getRiwayatJabatanASN($request, $nipBaru);
    if (!isset($jabatanFromSiasn['data'])) {
      $jabatanFromSiasn['data'] = [];
    } else if (gettype($jabatanFromSiasn['data']) != "array") {
      $jabatanFromSiasn['data'] = [];
    }
    $jabatanFromSiasn = $jabatanFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $jabatanFromSidak = DB::table('m_data_jabatan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $jabatanFromSidak = json_decode($jabatanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    if (count($jabatanFromSiasn) > 0) {
      for($i=0; $i<count($jabatanFromSidak); $i++) {
        if ($jabatanFromSidak[$i]['idBkn'] != '' && $jabatanFromSidak[$i]['idBkn'] != null) {
          $isFind = false;
          for($j=0; $j<count($jabatanFromSiasn); $j++) {
            if ($jabatanFromSidak[$i]['idBkn'] == $jabatanFromSiasn[$j]['id']) {
              $isFind = true;
            }
          }
          if (!$isFind) {
            // check di data angka kredit, kalo ada yg masih nyangkut, idDataJabatan di NULL-kan atau di delete row nya
            DB::table('m_data_angka_kredit')->where([
              ['idDataJabatan', '=', $jabatanFromSidak[$i]['id']],
              ['idBkn', '!=', '']
            ])->update([
              'idDataJabatan' => NULL
            ]);
            DB::table('m_data_angka_kredit')->where([
              ['idDataJabatan', '=', $jabatanFromSidak[$i]['id']],
              ['idBkn', '=', '']
            ])->delete();
            // end check di data angka kredit
            DB::table('m_data_jabatan')->where([
              ['idDataJabatanUpdate', '=', $jabatanFromSidak[$i]['id']]
            ])->delete();
            DB::table('m_data_jabatan')->where([
              ['id', '=', $jabatanFromSidak[$i]['id']]
            ])->delete();
          }
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newJabatanFromSiasn = [];
    $updateJabatanFromSiasn = [];
    for($i=0; $i<count($jabatanFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($jabatanFromSidak); $j++) {
        // cek
        if ($jabatanFromSiasn[$i]['id'] == $jabatanFromSidak[$j]['idBkn'] && ($jabatanFromSidak[$j]['idBkn'] !== '' || $jabatanFromSidak[$j]['idBkn'] !== null)) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newJabatanFromSiasn, $jabatanFromSiasn[$i]);
      } else {
        array_push($updateJabatanFromSiasn, $jabatanFromSiasn[$i]);
      }
    }

    ///// loop update jabatan dari siasn
    for($i=0; $i<count($updateJabatanFromSiasn); $i++) {
      $unorSidak = json_decode(DB::table('m_unit_organisasi')->where([
        ['idBkn', '=', $updateJabatanFromSiasn[$i]['unorId']]
      ])->get()->toJson(), true);
      $jabatanId = '';
      switch (intval($updateJabatanFromSiasn[$i]['jenisJabatan'])) {
        case 1:
          $idJenisJabatan = 1;
          $jabatanId = $updateJabatanFromSiasn[$i]['unorId'];
          $jabatanNama = $updateJabatanFromSiasn[$i]['namaJabatan'] ?? 'Kepala '.$updateJabatanFromSiasn[$i]['unorNama'];
          break;
        case 2:
          $idJenisJabatan = 2;
          $jabatanId = $updateJabatanFromSiasn[$i]['jabatanFungsionalId'];
          $jabatanNama = $updateJabatanFromSiasn[$i]['jabatanFungsionalNama'];
          break;
        case 4:
          $idJenisJabatan = 3;
          $jabatanId = $updateJabatanFromSiasn[$i]['jabatanFungsionalUmumId'];
          $jabatanNama = $updateJabatanFromSiasn[$i]['jabatanFungsionalUmumNama'];
          break;
        default:
          break;
      }
      if ($jabatanId == '') continue;
      $jabatanSidak = json_decode(DB::table('m_jabatan')->where([
        ['kodeKomponen', '=', $unorSidak[0]['kodeKomponen']],
        ['idBkn', '=', $jabatanId]
      ])->get()->toJson(), true);

      // cek jabatanId ada dalam Peta Jabatan sekarang atau tidak
      $idJabatan = 0;
      if (count($jabatanSidak) === 0) {
        $idJabatan = DB::table('m_jabatan')->insertGetId([
          'id' => NULL,
          'nama' => $jabatanNama,
          'kebutuhan' => -1,
          'idKelasJabatan' => 1,
          'target' => 0,
          'kodeKomponen' => $unorSidak[0]['kodeKomponen'],
          'idJenisJabatan' => $idJenisJabatan,
          'idEselon' => 1,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'idBkn' => $jabatanId
        ]);
      } else {
        $idJabatan = intval($jabatanSidak[0]['id']);
      }
      $affected = DB::table('m_data_jabatan')->where([
        ['idBkn', '=', $updateJabatanFromSiasn[$i]['id']],
        ['idPegawai', '=', intval($idPegawai)]
      ])->update([
        'idJabatan' => intval($idJabatan),
        'tmt' => date('Y-m-d', strtotime($updateJabatanFromSiasn[$i]['tmtJabatan'])),
        'spmt' => date('Y-m-d', strtotime($updateJabatanFromSiasn[$i]['tmtJabatan'])),
        'tanggalDokumen' => date('Y-m-d', strtotime($updateJabatanFromSiasn[$i]['tanggalSk'])),
        'nomorDokumen' => $updateJabatanFromSiasn[$i]['nomorSk'],
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

    ///// loop insert jabatan dari siasn, tetapi cek terlebih dahulu, idUnor dan idJabatan ada tidak dalam peta jabatan saat ini
    for($i=0; $i<count($newJabatanFromSiasn); $i++) {
      $unorSidak = json_decode(DB::table('m_unit_organisasi')->where(
        $newJabatanFromSiasn[$i]['unorId'] === '' ? [['idBkn', '=', $newJabatanFromSiasn[$i]['unorId']],['nama', '=', $newJabatanFromSiasn[$i]['namaUnor']]] : [['idBkn', '=', $newJabatanFromSiasn[$i]['unorId']]])->get()->toJson(), true);
      // cek unorId ada dalam SOTK sekarang atau tidak
      if (count($unorSidak) === 0) {
        $newId = DB::table('m_unit_organisasi')->insertGetId([
          'id' => NULL,
          'nama' => $newJabatanFromSiasn[$i]['unorNama'] === '' ? $newJabatanFromSiasn[$i]['namaUnor'] : $newJabatanFromSiasn[$i]['unorNama'],
          'kodeKomponen' => '',
          'digunakanSotkSekarang' => 0,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'idBkn' => $newJabatanFromSiasn[$i]['unorId'],
          'idBknAtasan' => $newJabatanFromSiasn[$i]['unorIndukId'],
        ]);
        DB::table('m_unit_organisasi')->where([
          ['id', '=', $newId]
        ])->update([
          'kodeKomponen' => '-'.$newId,
        ]);
        $unorSidak = json_decode(DB::table('m_unit_organisasi')->where([
          ['id', '=', $newId]
        ])->get()->toJson(), true);
      }
        $jabatanId = null;
        switch (intval($newJabatanFromSiasn[$i]['jenisJabatan'])) {
          case 1:
            $idJenisJabatan = 1;
            $jabatanId = $newJabatanFromSiasn[$i]['unorId'];
            if ($newJabatanFromSiasn[$i]['namaJabatan'] !== null && $newJabatanFromSiasn[$i]['namaJabatan'] !== '') {
              $jabatanNama = $newJabatanFromSiasn[$i]['namaJabatan'];
            } else {
              $jabatanNama = 'Kepala '.($newJabatanFromSiasn[$i]['unorNama'] === '' ? $newJabatanFromSiasn[$i]['namaUnor'] : $newJabatanFromSiasn[$i]['unorNama']);
            }
            break;
          case 2:
            $idJenisJabatan = 2;
            $jabatanId = $newJabatanFromSiasn[$i]['jabatanFungsionalId'];
            $jabatanNama = $newJabatanFromSiasn[$i]['jabatanFungsionalNama'];
            break;
          case 4:
            $idJenisJabatan = 3;
            $jabatanId = $newJabatanFromSiasn[$i]['jabatanFungsionalUmumId'];
            $jabatanNama = $newJabatanFromSiasn[$i]['jabatanFungsionalUmumNama'];
            break;
          default:
            break;
        }
        if ($jabatanId === null) continue;
        $jabatanSidak = json_decode(DB::table('m_jabatan')->where(
          $jabatanId !== null && $jabatanId !== '' ?
          [['kodeKomponen', '=', $unorSidak[0]['kodeKomponen']],['idBkn', '=', $jabatanId]]
          : [['kodeKomponen', '=', $unorSidak[0]['kodeKomponen']],['nama', '=', $jabatanNama]])->get()->toJson(), true);

        // cek jabatanId ada dalam Peta Jabatan sekarang atau tidak
        $idJabatan = 0;
        if (count($jabatanSidak) === 0) {
          $idJabatan = DB::table('m_jabatan')->insertGetId([
            'id' => NULL,
            'nama' => $jabatanNama,
            'kebutuhan' => -1,
            'idKelasJabatan' => 1,
            'target' => 0,
            'kodeKomponen' => $unorSidak[0]['kodeKomponen'],
            'idJenisJabatan' => $idJenisJabatan,
            'idEselon' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'idBkn' => $jabatanId
          ]);
        } else {
          $idJabatan = intval($jabatanSidak[0]['id']);
        }
        $affected = DB::table('m_data_jabatan')->insert([
          'id' => NULL,
          'idJabatan' => intval($idJabatan),
          'isPltPlh' => 0,
          'idJabatanTugasTambahan' => NULL,
          'tmt' => date('Y-m-d', strtotime($newJabatanFromSiasn[$i]['tmtJabatan'])),
          'spmt' => date('Y-m-d', strtotime($newJabatanFromSiasn[$i]['tmtJabatan'])),
          'tanggalDokumen' => date('Y-m-d', strtotime($newJabatanFromSiasn[$i]['tanggalSk'])),
          'nomorDokumen' => $newJabatanFromSiasn[$i]['nomorSk'],
          'idDokumen' => NULL,
          'idPegawai' => intval($idPegawai),
          'idUsulan' => 1,
          'idUsulanStatus' => 4,
          'idUsulanHasil' => 1,
          'idDataJabatanUpdate' => NULL,
          'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'idBkn' => $newJabatanFromSiasn[$i]['id'],
        ]);
    }

    $getDataJabatan = (new DataJabatanController)->getDataJabatan($request, $idPegawai);

    $callback = [
      'message' => "Data jabatan sudah berhasil disinkronisasi dari MySAPK.\nData yang dapat disinkronisasi adalah data sesuai dengan SOTK dan Peta Jabatan saat ini.\nJika setelah sinkronisasi tidak ada jabatan yang muncul, silahkan tambahkan jabatan sesuai dengan dasar SK Jabatan Definitif Terakhir.\n Dan jika terdapat ketidaksesuaian jabatan, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataJabatan['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function syncJabatanASNAll(Request $request, $from, $to) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $allPegawai = json_decode(DB::table('m_pegawai')->get()->toJson(), true);
    for($i=($from-1); $i<($to-1); $i++) {
      $this->syncJabatanASN($request, $allPegawai[$i]['id']);
    }
    return [
      'message' => "Sinkron ".($to-$from+1)." pegawai telah berhasil.",
      'status' => 2
    ];
  }
  public function insertRiwayatJabatan($idUsulan) {
    $usulan = json_decode(DB::table('m_data_jabatan')->where([
      ['id', '=', $idUsulan]
    ])->get()->toJson(), true)[0];
    $jabatan = json_decode(DB::table('m_jabatan')->where([
      ['id', '=', $usulan['idJabatan']]
    ])->get()->toJson(), true)[0];
    $eselon = json_decode(DB::table('m_eselon')->where([
      ['id', '=', $jabatan['idEselon']]
    ])->get()->toJson(), true)[0];
    $jenisJabatan = json_decode(DB::table('m_jenis_jabatan')->where([
      ['id', '=', $jabatan['idJenisJabatan']]
    ])->get()->toJson(), true)[0];
    $asn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $usulan['idPegawai']]
    ])->get()->toJson(), true)[0];
    $unor = json_decode(DB::table('m_unit_organisasi')->where([
      ['kodeKomponen', '=', $jabatan['kodeKomponen']]
    ])->get()->toJson(), true)[0];
    $data = [
      'eselonId' => $eselon['idBkn'],
      'jabatanId' => $jabatan['idBkn'],
      'jenisJabatan' => $jenisJabatan['idBkn'],
      'nomorSk' => $usulan['nomorDokumen'],
      'pnsId' => $asn['idBkn'],
      'tanggalSk' => date('d-m-Y', strtotime($usulan['tanggalDokumen'])),
      'tmtJabatan' => date('d-m-Y', strtotime($usulan['tmt'])),
      'tmtPelantikan' => date('d-m-Y', strtotime($usulan['tmt'])),
      'unorId' => $unor['idBkn']
    ];
    $response = $this->insertRiwayatJabatanASN($data);
    return $response;
    // return $data;
  }
  public function syncDiklatASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat diklat dari siasn
    $diklatFromSiasn = $this->getRiwayatDiklatASN($request, $nipBaru);
    $diklatFromSiasn = $diklatFromSiasn['data'] ?? [];

    ///// get data riwayat kursus dari siasn
    $kursusFromSiasn = $this->getRiwayatKursusASN($request, $nipBaru);
    $kursusFromSiasn = $kursusFromSiasn['data'] ?? [];

    ///// get diklat asn dari sidak
    $diklatFromSidak = DB::table('m_data_diklat')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $diklatFromSidak = json_decode($diklatFromSidak, true);

    ///// cek apakah diklat yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newDiklatFromSiasn
    $newDiklatFromSiasn = [];
    for($i=0; $i<count($diklatFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($diklatFromSidak); $j++) {
        // cek
        if ($diklatFromSiasn[$i]['id'] == $diklatFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newDiklatFromSiasn, $diklatFromSiasn[$i]);
      }
    }

    ///// cek apakah kursus yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newKursusFromSiasn
    $newKursusFromSiasn = [];
    for($i=0; $i<count($kursusFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($diklatFromSidak); $j++) {
        // cek
        if ($kursusFromSiasn[$i]['id'] == $diklatFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newKursusFromSiasn, $kursusFromSiasn[$i]);
      }
    }

    ///// loop insert diklat dari siasn
    for ($i=0; $i<count($newDiklatFromSiasn); $i++) {
      DB::table('m_data_diklat')->insert([
        'id' => NULL,
        'idJenisDiklat' => 1,
        'idDaftarDiklat' => $newDiklatFromSiasn[$i]['latihanStrukturalId'],
        'namaDiklat' => $newDiklatFromSiasn[$i]['latihanStrukturalNama'],
        'lamaDiklat' => $newDiklatFromSiasn[$i]['jumlahJam'] ?? 0,
        'tanggalDiklat' => date('Y-m-d', strtotime($newDiklatFromSiasn[$i]['tanggal'])),
        'tanggalSelesaiDiklat' => date('Y-m-d', strtotime($newDiklatFromSiasn[$i]['tanggalSelesai'])),
        'idDaftarInstansiDiklat' => 1,
        'institusiPenyelenggara' => $newDiklatFromSiasn[$i]['institusiPenyelenggara'],
        'nomorDokumen' => $newDiklatFromSiasn[$i]['nomor'],
        'idDokumen' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataDiklatUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newDiklatFromSiasn[$i]['id'],
      ]);
    }
    for ($i=0; $i<count($newKursusFromSiasn); $i++) {
      DB::table('m_data_diklat')->insert([
        'id' => NULL,
        'idJenisDiklat' => $newKursusFromSiasn[$i]['jenisDiklatId'] == '' ? 4 : $newKursusFromSiasn[$i]['jenisDiklatId'],
        'idDaftarDiklat' => 1296,
        'namaDiklat' => $newKursusFromSiasn[$i]['namaKursus'],
        'lamaDiklat' => $newKursusFromSiasn[$i]['jumlahJam'] ?? 0,
        'tanggalDiklat' => $newKursusFromSiasn[$i]['tanggalKursus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($newKursusFromSiasn[$i]['tanggalKursus'])),
        'tanggalSelesaiDiklat' => isset($tanggalSelesaiDiklat) ? date('Y-m-d', strtotime($newKursusFromSiasn[$i]['tanggalSelesaiKursus'])) : '0000-00-00',
        'idDaftarInstansiDiklat' => 1,
        'institusiPenyelenggara' => $newKursusFromSiasn[$i]['institusiPenyelenggara'],
        'nomorDokumen' => $newKursusFromSiasn[$i]['noSertipikat'],
        'idDokumen' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataDiklatUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newKursusFromSiasn[$i]['id'],
      ]);
    }

    $getDataDiklat = (new DataDiklatController)->getDataDiklat($request, $idPegawai);

    $callback = [
      'message' => "Data diklat/kursus sudah berhasil disinkronisasi dari MySAPK.\nJika setelah sinkronisasi tidak ada diklat/kursus yang muncul, silahkan tambahkan diklat/kursus sesuai dengan dasar Sertifikat yang dimiliki.\n Dan jika terdapat ketidaksesuaian diklat/kursus, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataDiklat['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function insertRiwayatDiklatKursus($idUsulan) {
    $usulan = json_decode(DB::table('m_data_diklat')->where([
      ['id', '=', $idUsulan]
    ])->get(), true)[0];
    $asn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $usulan['idPegawai']]
    ])->get()->toJson(), true)[0];
    $data = [];
    $daftarDiklat = json_decode(DB::table('m_daftar_diklat')->where([
      ['id', '=', intval($usulan['idDaftarDiklat'])]
    ])->get(), true)[0];
    $response = [];
    if (intval($usulan['idJenisDiklat']) === 1) {
      $data = [
        'institusiPenyelenggara' => $usulan['institusiPenyelenggara'],
        'jumlahJam' => intval($usulan['lamaDiklat']),
        'latihanStrukturalId' => $daftarDiklat['idBkn'],
        'nomor' => $usulan['nomorDokumen'],
        'pnsOrangId' => $asn['idBkn'],
        'tahun' => intval(date('Y', strtotime($usulan['tanggalDiklat']))),
        'tanggal' => date('d-m-Y', strtotime($usulan['tanggalDiklat'])),
        'tanggalSelesai' => date('d-m-Y', strtotime($usulan['tanggalSelesaiDiklat'])),
      ];
      $response = $this->insertRiwayatDiklatASN($data);
      // {
      //   "success": true,
      //   "mapData": {
      //       "rwDiklatId": "8d41cb9c-0cbd-11ee-97bb-0a580a83003e"
      //   },
      //   "message": "success"
      // }
    } else {
      $data = [
        'institusiPenyelenggara' => $usulan['institusiPenyelenggara'],
        'jenisDiklatId' => $usulan['idJenisDiklat'],
        'jenisKursus' => '',
        'jenisKursusSertipikat' => '',
        'jumlahJam' => intval($usulan['lamaDiklat']),
        'namaKursus' => $usulan['namaDiklat'],
        'nomorSertipikat' => $usulan['nomorDokumen'],
        'pnsOrangId' => $asn['idBkn'],
        'tahunKursus' => intval(date('Y', strtotime($usulan['tanggalDiklat']))),
        'tanggalKursus' => date('d-m-Y', strtotime($usulan['tanggalDiklat'])),
        'tanggalSelesaiKursus' => date('d-m-Y', strtotime($usulan['tanggalSelesaiDiklat'])),
      ];
      $response = $this->insertRiwayatKursusASN($data);
    }
    return $response;
  }
  public function syncPangkatGolonganASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $pangkatGolonganFromSiasn = $this->getRiwayatPangkatGolonganASN($request, $nipBaru);
    $pangkatGolonganFromSiasn = !isset($pangkatGolonganFromSiasn['data']) ? [] : $pangkatGolonganFromSiasn['data'];
    if ($pangkatGolonganFromSiasn == "Data tidak ditemukan") {
      return [
        'message' => 'Terjadi kesalahan pada server MySAPK.',
        'status' => 3
      ];
    }

    ///// get jabatan asn dari sidak
    $pangkatGolonganFromSidak = DB::table('m_data_pangkat')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $pangkatGolonganFromSidak = json_decode($pangkatGolonganFromSidak, true);

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newPangkatGolonganFromSiasn
    $newPangkatGolonganFromSiasn = [];
    for($i=0; $i<count($pangkatGolonganFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($pangkatGolonganFromSidak); $j++) {
        // cek
        if ($pangkatGolonganFromSiasn[$i]['id'] == $pangkatGolonganFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newPangkatGolonganFromSiasn, $pangkatGolonganFromSiasn[$i]);
      }
    }

    /// delete data pangkat di sidak jika di siasn tidak ada datanya
    if (count($pangkatGolonganFromSiasn) > 0) {
      for($i=0; $i<count($pangkatGolonganFromSidak); $i++) {
        $isFind = false;
        for($j=0; $j<count($pangkatGolonganFromSiasn); $j++) {
          if ($pangkatGolonganFromSidak[$i]['idBkn'] == $pangkatGolonganFromSiasn[$j]['id']) $isFind = true;
        }
        if (!$isFind) {
          DB::table('m_data_pangkat')->where([
            ['idDataPangkatUpdate', '=', $pangkatGolonganFromSidak[$i]['id']]
          ])->delete();
          DB::table('m_data_pangkat')->where([
            ['id', '=', $pangkatGolonganFromSidak[$i]['id']]
          ])->delete();
        }
      }
    }

    ///// loop insert pangkat/golongan dari siasn
    $affected = '';
    for($i=0; $i<count($newPangkatGolonganFromSiasn); $i++) {
      if ($newPangkatGolonganFromSiasn[$i]['jenisKPId'] == "") {
        continue;
      }
      $idJenisPangkat = json_decode(DB::table('m_jenis_pangkat')->where([
        ['idBkn', '=', $newPangkatGolonganFromSiasn[$i]['jenisKPId']]
      ])->get()->toJson(), true)[0];
      $idDaftarPangkat = json_decode(DB::table('m_daftar_pangkat')->where([
        ['idBkn', '=', $newPangkatGolonganFromSiasn[$i]['golonganId']]
      ])->get()->toJson(), true)[0];
      DB::table('m_data_pangkat')->insert([
        'id' => NULL,
        'idJenisPangkat' => intval($idJenisPangkat['id']),
        'idDaftarPangkat' => intval($idDaftarPangkat['id']),
        'masaKerjaTahun' => intval($newPangkatGolonganFromSiasn[$i]['masaKerjaGolonganTahun']),
        'masaKerjaBulan' => intval($newPangkatGolonganFromSiasn[$i]['masaKerjaGolonganBulan']),
        'nomorDokumen' => $newPangkatGolonganFromSiasn[$i]['skNomor'],
        'tanggalDokumen' => date('Y-m-d', strtotime($newPangkatGolonganFromSiasn[$i]['skTanggal'])),
        'tmt' => $newPangkatGolonganFromSiasn[$i]['tmtGolongan'],
        'nomorBkn' => $newPangkatGolonganFromSiasn[$i]['noPertekBkn'],
        'tanggalBkn' => date('Y-m-d', strtotime($newPangkatGolonganFromSiasn[$i]['tglPertekBkn'])),
        'idDokumen' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPangkatUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newPangkatGolonganFromSiasn[$i]['id'],
      ]);
    }

    $getDataPangkat = (new DataGolonganPangkatController)->getDataGolPang($request, $idPegawai);

    $callback = [
      'message' => "Data pangkat/golongan sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian pangkat/golongan, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataPangkat['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function syncPendidikanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat pendidikan dari siasn
    $pendidikanFromSiasn = $this->getRiwayatPendidikanASN($request, $nipBaru);
    if (!isset($pendidikanFromSiasn['data'])) {
      $pendidikanFromSiasn['data'] = [];
    } else if (gettype($pendidikanFromSiasn['data']) != "array") {
      $pendidikanFromSiasn['data'] = [];
    }
    $pendidikanFromSiasn = $pendidikanFromSiasn['data'];

    ///// get pendidikan asn dari sidak
    $pendidikanFromSidak = DB::table('m_data_pendidikan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $pendidikanFromSidak = json_decode($pendidikanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    if (count($pendidikanFromSiasn) > 0) {
      for($i=0; $i<count($pendidikanFromSidak); $i++) {
        if ($pendidikanFromSidak[$i]['idBkn'] != '' && $pendidikanFromSidak[$i]['idBkn'] != null) {
          $isFind = false;
          for($j=0; $j<count($pendidikanFromSiasn); $j++) {
            if ($pendidikanFromSidak[$i]['idBkn'] == $pendidikanFromSiasn[$j]['id']) {
              $isFind = true;
            }
          }
          if (!$isFind) {
            DB::table('m_data_pendidikan')->where([
              ['idDataPendidikanUpdate', '=', $pendidikanFromSidak[$i]['id']]
            ])->delete();
            DB::table('m_data_pendidikan')->where([
              ['id', '=', $pendidikanFromSidak[$i]['id']]
            ])->delete();
          }
        }
      }
    }

    $daftarTingkatPendidikan = json_decode(DB::table('m_tingkat_pendidikan')->get(), true);
    $daftarPendidikan = json_decode(DB::table('m_daftar_pendidikan')->get(), true);

    ///// cek apakah pendidikan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newPendidikanFromSiasn
    $newPendidikanFromSiasn = [];
    $updatePendidikanFromSiasn = [];
    $pendidikanPertamaSaatPns = null;
    for($i=0; $i<count($pendidikanFromSiasn); $i++) {
      /// cek data pendidikan pertama kali saat diangkat pns
      if (intval($pendidikanFromSiasn[$i]['isPendidikanPertama']) === 1) {
        $pendidikanPertamaSaatPns = $pendidikanFromSiasn[$i]['tkPendidikanId'];
      }
      $isFind = false;
      for($j=0; $j<count($pendidikanFromSidak); $j++) {
        // cek
        if ($pendidikanFromSiasn[$i]['id'] == $pendidikanFromSidak[$j]['idBkn']) {
          $isFind = true;
          $tingkatPendidikan = null;
          foreach ($daftarTingkatPendidikan as $tkPendidikan) {
            if ($tkPendidikan['idBkn'] == $pendidikanFromSiasn[$i]['tkPendidikanId']) $tingkatPendidikan = $tkPendidikan;
          }
          if ($tingkatPendidikan['id'] != $pendidikanFromSidak[$j]['idTingkatPendidikan']) {
            array_push($updatePendidikanFromSiasn, $pendidikanFromSiasn[$i]);
          }
        }
      }
      if (!$isFind/* && $pendidikanFromSiasn[$i]['namaSekolah'] != null && $pendidikanFromSiasn[$i]['nomorIjasah'] != null*/) {
        array_push($newPendidikanFromSiasn, $pendidikanFromSiasn[$i]);
      }
    }

    ///// loop update jika ada tingkat pendidikan dari sidak yg tidak sama dengan siasn
    $affected = '';
    for($i=0; $i<count($updatePendidikanFromSiasn); $i++) {
      $idJenisPendidikan = 1;
      if ($pendidikanPertamaSaatPns != null) {
        if (intval($pendidikanPertamaSaatPns) > intval($tingkatPendidikan['id'])) $idJenisPendidikan = 3;
        else if (intval($pendidikanPertamaSaatPns) === intval($tingkatPendidikan['id'])) $idJenisPendidikan = 2;
      }
      $tingkatPendidikan = null;
      foreach ($daftarTingkatPendidikan as $tkPendidikan) {
        if ($tkPendidikan['idBkn'] == $updatePendidikanFromSiasn[$i]['tkPendidikanId']) $tingkatPendidikan = $tkPendidikan;
      }
      $pendidikan = null;
      foreach ($daftarPendidikan as $pddkn) {
        if ($pddkn['idBkn'] == $updatePendidikanFromSiasn[$i]['pendidikanId']) $pendidikan = $pddkn;
      }
      DB::table('m_data_pendidikan')->where([
        ['idBkn', '=', $updatePendidikanFromSiasn[$i]['id']],
        ['idPegawai', '=', $idPegawai]
      ])->update([
        'idJenisPendidikan' => intval($updatePendidikanFromSiasn[$i]['isPendidikanPertama']) > 0 ? 2 : $idJenisPendidikan,
        'idTingkatPendidikan' => $tingkatPendidikan['id'],
        'idDaftarPendidikan' => $pendidikan['id'],
        'namaSekolah' => $updatePendidikanFromSiasn[$i]['namaSekolah'] ?? '',
        'gelarDepan' => $updatePendidikanFromSiasn[$i]['gelarDepan'] ?? '',
        'gelarBelakang' => $updatePendidikanFromSiasn[$i]['gelarBelakang'] ?? '',
        'tanggalLulus' => $updatePendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($updatePendidikanFromSiasn[$i]['tglLulus'])),
        'tahunLulus' => $updatePendidikanFromSiasn[$i]['tahunLulus'] ?? 1111,
        'nomorDokumen' => $updatePendidikanFromSiasn[$i]['nomorIjasah'] ?? '',
        'tanggalDokumen' => $updatePendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($updatePendidikanFromSiasn[$i]['tglLulus'])),
        'idDokumen' => NULL,
        'idDokumenTranskrip' => NULL,
        'idDataPendidikanUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

    ///// loop insert pangkat/golongan dari siasn
    $affected = '';
    for($i=0; $i<count($newPendidikanFromSiasn); $i++) {
      $idJenisPendidikan = 1;
      if ($pendidikanPertamaSaatPns != null) {
        if (intval($pendidikanPertamaSaatPns) > intval($tingkatPendidikan['id'])) $idJenisPendidikan = 3;
        else if (intval($pendidikanPertamaSaatPns) === intval($tingkatPendidikan['id'])) $idJenisPendidikan = 2;
      }
      $tingkatPendidikan = null;
      foreach ($daftarTingkatPendidikan as $tkPendidikan) {
        if ($tkPendidikan['idBkn'] == $newPendidikanFromSiasn[$i]['tkPendidikanId']) $tingkatPendidikan = $tkPendidikan;
      }
      $pendidikan = null;
      foreach ($daftarPendidikan as $pddkn) {
        if ($pddkn['idBkn'] == $newPendidikanFromSiasn[$i]['pendidikanId']) $pendidikan = $pddkn;
      }
      DB::table('m_data_pendidikan')->insert([
        'id' => NULL,
        'idJenisPendidikan' => intval($newPendidikanFromSiasn[$i]['isPendidikanPertama']) > 0 ? 2 : $idJenisPendidikan,
        'idTingkatPendidikan' => $tingkatPendidikan['id'],
        'idDaftarPendidikan' => $pendidikan['id'],
        'namaSekolah' => $newPendidikanFromSiasn[$i]['namaSekolah'] ?? '',
        'gelarDepan' => $newPendidikanFromSiasn[$i]['gelarDepan'] ?? '',
        'gelarBelakang' => $newPendidikanFromSiasn[$i]['gelarBelakang'] ?? '',
        'tanggalLulus' => $newPendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($newPendidikanFromSiasn[$i]['tglLulus'])),
        'tahunLulus' => $newPendidikanFromSiasn[$i]['tahunLulus'] ?? 1111,
        'nomorDokumen' => $newPendidikanFromSiasn[$i]['nomorIjasah'] ?? '',
        'tanggalDokumen' => $newPendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($newPendidikanFromSiasn[$i]['tglLulus'])),
        'idDokumen' => NULL,
        'idDokumenTranskrip' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPendidikanUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newPendidikanFromSiasn[$i]['id'],
      ]);
    }

    $getDataPendidikan = (new DataPendidikanController)->getDataPendidikan($request, $idPegawai);

    $callback = [
      'message' => "Data pendidikan sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian pendidikan, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataPendidikan['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function syncHukdisASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $hukdisFromSiasn = $this->getRiwayatHukdisASN($nipBaru);
    $hukdisFromSiasn = !isset($hukdisFromSiasn['data']) ? [] : $hukdisFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $hukdisFromSidak = DB::table('m_data_hukuman_disiplin')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $hukdisFromSidak = json_decode($hukdisFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    if (count($hukdisFromSiasn) > 0) {
      for($i=0; $i<count($hukdisFromSidak); $i++) {
        if ($hukdisFromSidak[$i]['idBkn'] != '' && $hukdisFromSidak[$i]['idBkn'] != null) {
          $isFind = false;
          for($j=0; $j<count($hukdisFromSiasn); $j++) {
            if ($hukdisFromSidak[$i]['idBkn'] == $hukdisFromSiasn[$j]['id']) {
              $isFind = true;
            }
          }
          if (!$isFind) {
            DB::table('m_data_hukuman_disiplin')->where([
              ['idDataHukumanDisiplinUpdate', '=', $hukdisFromSidak[$i]['id']]
            ])->delete();
            DB::table('m_data_hukuman_disiplin')->where([
              ['id', '=', $hukdisFromSidak[$i]['id']]
            ])->delete();
          }
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newHukdisFromSiasn = [];
    for($i=0; $i<count($hukdisFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($hukdisFromSidak); $j++) {
        // cek
        if ($hukdisFromSiasn[$i]['id'] == $hukdisFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newHukdisFromSiasn, $hukdisFromSiasn[$i]);
      }
    }

    ///// loop insert jabatan dari siasn, tetapi cek terlebih dahulu, idUnor dan idJabatan ada tidak dalam peta jabatan saat ini
    $affected = '';
    for($i=0; $i<count($newHukdisFromSiasn); $i++) {
      if ($newHukdisFromSiasn[$i]['jenisHukuman'] === 'R' || $newHukdisFromSiasn[$i]['jenisHukuman'] === 'S' || $newHukdisFromSiasn[$i]['jenisHukuman'] === 'B') {
        switch ($newHukdisFromSiasn[$i]['jenisTingkatHukumanId']) {
          case 'R':
            $idDaftarHukumanDisiplin = 15;
            break;
          case 'S':
            $idDaftarHukumanDisiplin = 16;
            break;
          default:
            $idDaftarHukumanDisiplin = 17;
            break;
        }
      } else {
        $idDaftarHukumanDisiplin = json_decode(DB::table('m_daftar_hukuman_disiplin')->where([
          ['idBkn', '=', intval($newHukdisFromSiasn[$i]['jenisHukuman'])]
        ])->get(), true)[0]['id'];
      }
      $idDaftarDasarHukumHukdis = json_decode(DB::table('m_daftar_dasar_hukum_hukuman_disiplin')->where([
        ['idBkn', '=', $newHukdisFromSiasn[$i]['nomorPp']]
      ])->orWhere('nama', '=', $newHukdisFromSiasn[$i]['nomorPp'])->get(), true)[0];
      $idJenisHukuman = json_decode(DB::table('m_jenis_hukuman_disiplin')->where('idBkn', '=', $newHukdisFromSiasn[$i]['jenisTingkatHukumanId'])->orWhere('idBkn', '=', $newHukdisFromSiasn[$i]['jenisHukuman'])->get(), true)[0];
      $idDaftarAlasanHukdis = json_decode(DB::table('m_daftar_alasan_hukuman_disiplin')->where('idBkn', '=', $newHukdisFromSiasn[$i]['alasanHukumanDisiplin'])->get(), true)[0];
      DB::table('m_data_hukuman_disiplin')->insert([
        'id' => NULL,
        'idJenisHukumanDisiplin' => $idJenisHukuman['id'],
        'idDaftarHukumanDisiplin' => $idDaftarHukumanDisiplin,
        'nomorDokumen' => $newHukdisFromSiasn[$i]['skNomor'],
        'tanggalDokumen' => date('Y-m-d', strtotime($newHukdisFromSiasn[$i]['skTanggal'])),
        'tmtAwal' => date('Y-m-d', strtotime($newHukdisFromSiasn[$i]['hukumanTanggal'])),
        'masaHukuman' => (intval($newHukdisFromSiasn[$i]['masaTahun']) * 12) + intval($newHukdisFromSiasn[$i]['masaBulan']),
        'tmtAkhir' => date('Y-m-d', strtotime($newHukdisFromSiasn[$i]['akhirHukumTanggal'])),
        'idDaftarDasarHukumHukdis' => $idDaftarDasarHukumHukdis['id'],
        'idDaftarAlasanHukdis' => $idDaftarAlasanHukdis['id'],
        'keteranganAlasanHukdis' => $newHukdisFromSiasn[$i]['keterangan'],
        'idDokumen' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataHukumanDisiplinUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newHukdisFromSiasn[$i]['id'],
      ]);
    }

    $getDataHukdis = (new DataHukumanDisiplinController)->getDataHukdis($idPegawai, null, $request);

    $callback = [
      'message' => "Data hukuman disiplin sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataHukdis['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function syncPenghargaanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return [
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ];
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $penghargaanFromSiasn = $this->getRiwayatPenghargaanASN($request, $nipBaru);
    $penghargaanFromSiasn = $penghargaanFromSiasn['data'] == 'Data tidak ditemukan' || !isset($penghargaanFromSiasn['data']) ? [] : $penghargaanFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $penghargaanFromSidak = DB::table('m_data_penghargaan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $penghargaanFromSidak = json_decode($penghargaanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    if (count($penghargaanFromSiasn) > 0) {
      for($i=0; $i<count($penghargaanFromSidak); $i++) {
        if ($penghargaanFromSidak[$i]['idBkn'] != '' && $penghargaanFromSidak[$i]['idBkn'] != null) {
          $isFind = false;
          for($j=0; $j<count($penghargaanFromSiasn); $j++) {
            if ($penghargaanFromSidak[$i]['idBkn'] == $penghargaanFromSiasn[$j]['ID']) {
              $isFind = true;
            }
          }
          if (!$isFind) {
            DB::table('m_data_penghargaan')->where([
              ['idDataPenghargaanUpdate', '=', $penghargaanFromSidak[$i]['id']]
            ])->delete();
            DB::table('m_data_penghargaan')->where([
              ['id', '=', $penghargaanFromSidak[$i]['id']]
            ])->delete();
          }
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newPenghargaanFromSiasn = [];
    for($i=0; $i<count($penghargaanFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($penghargaanFromSidak); $j++) {
        // cek
        if ($penghargaanFromSiasn[$i]['ID'] == $penghargaanFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newPenghargaanFromSiasn, $penghargaanFromSiasn[$i]);
      }
    }

    ///// loop insert jabatan dari siasn, tetapi cek terlebih dahulu, idUnor dan idJabatan ada tidak dalam peta jabatan saat ini
    $affected = '';
    for($i=0; $i<count($newPenghargaanFromSiasn); $i++) {
      $idDaftarJenisPenghargaan = json_decode(DB::table('m_daftar_jenis_penghargaan')->where([
        ['idBkn', '=', $newPenghargaanFromSiasn[$i]['hargaId']]
      ])->get(), true)[0];
      DB::table('m_data_penghargaan')->insert([
        'id' => NULL,
        'tahunPenghargaan' => intval($newPenghargaanFromSiasn[$i]['tahun']),
        'idDaftarJenisPenghargaan' => $idDaftarJenisPenghargaan['id'],
        'tanggalDokumen' => date('Y-m-d', strtotime($newPenghargaanFromSiasn[$i]['skDate'])),
        'nomorDokumen' => $newPenghargaanFromSiasn[$i]['skNomor'],
        'idDokumen' => NULL,
        'idPegawai' => intval($idPegawai),
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataPenghargaanUpdate' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $newPenghargaanFromSiasn[$i]['ID'],
      ]);
    }

    $getDataPenghargaan = (new DataPenghargaanController)->getDataPenghargaan($request, $idPegawai);

    $callback = [
      'message' => "Data hukuman disiplin sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataPenghargaan['message'],
      'status' => 2
    ];
    return $callback;
  }
  public function insertRiwayatPenghargaan($idUsulan) {
    $usulan = json_decode(DB::table('m_data_penghargaan')->where([
      ['id', '=', $idUsulan]
    ])->get(), true)[0];
    $asn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $usulan['idPegawai']]
    ])->get()->toJson(), true)[0];
    $data = [];
    $daftarJenisPenghargaan = json_decode(DB::table('m_daftar_jenis_penghargaan')->where([
      ['id', '=', intval($usulan['idDaftarJenisPenghargaan'])]
    ])->get(), true)[0];
    $response = [];
    $data = [
      'hargaId' => $daftarJenisPenghargaan['idBkn'],
      'pnsOrangId' => $asn['idBkn'],
      'skDate' => date('d-m-Y', strtotime($usulan['tanggalDokumen'])),
      'skNomor' => $usulan['nomorDokumen'],
      'tahun' => intval($usulan['tahunPenghargaan'])
    ];
    $response = $this->insertRiwayatPenghargaanASN($data);
    return $response;
  }
  public function insertRiwayatAngkaKredit($idUsulan) {
    $usulan = json_decode(DB::table('m_data_angka_kredit')->join('m_pegawai', 'm_data_angka_kredit.idPegawai', '=', 'm_pegawai.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->where([
      ['m_data_jabatan.idBkn', '!=', ''],
      ['m_pegawai.idBkn', '!=', ''],
      ['m_data_angka_kredit.id', '=', $idUsulan]
    ])->get([
      'm_data_angka_kredit.*',
      'm_pegawai.idBkn as idBknAsn',
      'm_data_jabatan.idBkn as idBknJabatan'
    ]), true)[0];
    $dataToSiasn = [
      'bulanMulaiPenailan' => date_parse_from_format('Y-m-d', $usulan['periodePenilaianMulai'])['month'].'',
      'bulanSelesaiPenailan' => date_parse_from_format('Y-m-d', $usulan['periodePenilaianSelesai'])['month'].'',
      'tahunMulaiPenailan' => date_parse_from_format('Y-m-d', $usulan['periodePenilaianMulai'])['year'].'',
      'tahunSelesaiPenailan' => date_parse_from_format('Y-m-d', $usulan['periodePenilaianSelesai'])['year'].'',
      'isAngkaKreditPertama' => intval($usulan['idDaftarJenisAngkaKredit']) === 1 ? '1' : '0',
      'isIntegrasi' => intval($usulan['idDaftarJenisAngkaKredit']) === 2 ? '1' : '0',
      'isKonversi' => intval($usulan['idDaftarJenisAngkaKredit']) === 3 ? '1' : '0',
      'kreditBaruTotal' => $usulan['angkaKreditTotal'],
      'kreditPenunjangBaru' => $usulan['angkaKreditPenunjang'] ?? '0',
      'kreditUtamaBaru' => $usulan['angkaKreditUtama'] ?? '0',
      'nomorSk' => $usulan['nomorDokumen'],
      'pnsId' => $usulan['idBknAsn'],
      'rwJabatanId' => $usulan['idBknJabatan'],
      'tanggalSk' => date('d-m-Y', strtotime($usulan['tanggalDokumen']))
    ];
    $response = $this->insertRiwayatAngkaKreditASN($dataToSiasn);
    return $response;
  }
  public function syncSkpASN(Request $request, $idPegawai) {
    // $authenticated = $this->isAuth($request)['authenticated'];
    // $username = $this->isAuth($request)['username'];
    // if(!$authenticated) return [
    //   'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
    //   'status' => $authenticated === true ? 1 : 0
    // ];

    $getAsn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get(), true);
    // if (count($getAsn) === 0) {
    //   return [
    //     'message' => 'Data ASN tidak ditemukan.',
    //     'status' => 3
    //   ]));
    // }
    $nipBaru = $getAsn[0]['nip'];

    $skpFromSiasn = $this->getRiwayatSkpASN($request, $nipBaru);

    if(!isset($skpFromSiasn['skp']['data'])) $skpFromSiasn['skp']['data'] = [];
    if(!isset($skpFromSiasn['skp2022']['data'])) $skpFromSiasn['skp2022']['data'] = [];

    $skpFromSidak = [
      'skp' => json_decode(DB::table('m_data_skp')->where([
        ['idPegawai', '=', $idPegawai]
      ])->get(), true),
      'skp2022' => json_decode(DB::table('m_data_skp_2022')->where([
        ['idPegawai', '=', $idPegawai]
      ])->get(), true)
    ];

    /// check jika tidak ada di siasn tp ada di sidak ada
    $countSkpSidak = count($skpFromSidak['skp']);
    $countSkp2022Sidak = count($skpFromSidak['skp2022']);
    $lengthIterationSidak = $countSkpSidak > $countSkp2022Sidak ? $countSkpSidak : $countSkp2022Sidak;
    $countSkpSiasn = count($skpFromSiasn['skp']['data']);
    $countSkp2022Siasn = count($skpFromSiasn['skp2022']['data']);
    $lengthIterationSiasn = $countSkpSiasn > $countSkp2022Siasn ? $countSkpSiasn : $countSkp2022Siasn;
    $listDeletedSkp = [];
    $listDeletedSkp2022 = [];
    for ($i = 0; $i < $lengthIterationSidak; $i++) {
      $isSkpFound = false;
      $isSkp2022Found = false;
      for ($j = 0; $j < $lengthIterationSiasn; $j++) {
        if ($i < $countSkpSidak && $j < $countSkpSiasn) {
          if (($skpFromSidak['skp'][$i]['idBkn'] !== '' || $skpFromSidak['skp'][$i]['idBkn'] !== null) && $skpFromSidak['skp'][$i]['idBkn'] === $skpFromSiasn['skp']['data'][$j]['id']) $isSkpFound = true;
        }
        if ($i < $countSkp2022Sidak && $j < $countSkp2022Siasn) {
          if (($skpFromSidak['skp2022'][$i]['idBkn'] !== '' || $skpFromSidak['skp2022'][$i]['idBkn'] !== null) && $skpFromSidak['skp2022'][$i]['idBkn'] === $skpFromSiasn['skp2022']['data'][$j]['id']) $isSkp2022Found = true;
        }
      }
      if (!$isSkpFound && $i < $countSkpSidak) {
        DB::table('m_data_skp')->where([['id', '=', $skpFromSidak['skp'][$i]['id']]])->delete();
        array_push($listDeletedSkp, $i);
      }
      if (!$isSkp2022Found && $i < $countSkp2022Sidak) {
        DB::table('m_data_skp_2022')->where([['id', '=', $skpFromSidak['skp2022'][$i]['id']]])->delete();
        array_push($listDeletedSkp2022, $i);
      }
    }

    $countListDeletedSkp = count($listDeletedSkp);
    $countListDeletedSkp2022 = count($listDeletedSkp2022);
    if ($countListDeletedSkp > 0) {
      for ($i = 0; $i < $countListDeletedSkp; $i++) {
        unset($skpFromSidak['skp'][$listDeletedSkp[$i]]);
      }
      $skpFromSidak['skp'] = array_values($skpFromSidak['skp']);
      $countSkpSidak = count($skpFromSidak['skp']);
    }
    if ($countListDeletedSkp2022 > 0) {
      for ($i = 0; $i < $countListDeletedSkp2022; $i++) {
        unset($skpFromSidak['skp2022'][$listDeletedSkp2022[$i]]);
      }
      $skpFromSidak['skp2022'] = array_values($skpFromSidak['skp2022']);
      $countSkp2022Sidak = count($skpFromSidak['skp2022']);
    }
    $lengthIterationSidak = $countSkpSidak > $countSkp2022Sidak ? $countSkpSidak : $countSkp2022Sidak;

    $daftarGolongan = json_decode(DB::table('m_daftar_pangkat')->get(), true);
    $daftarJenisJabatan = json_decode(DB::table('m_jenis_jabatan')->get(), true);
    $daftarJenisPeraturan = json_decode(DB::table('m_jenis_peraturan_kinerja')->get(), true);

    /// check yg dari siasn
    for ($i = 0; $i < $lengthIterationSiasn; $i++) {
      $isSkpFound = false;
      $isSkp2022Found = false;
      for ($j = 0; $j < $lengthIterationSidak; $j++) {
        if ($i < $countSkpSiasn && $j < $countSkpSidak) {
          if ($skpFromSiasn['skp']['data'][$i]['id'] === $skpFromSidak['skp'][$j]['idBkn']) $isSkpFound = true;
        }
        if ($i < $countSkp2022Siasn && $j < $countSkp2022Sidak) {
          if ($skpFromSiasn['skp2022']['data'][$i]['id'] === $skpFromSidak['skp2022'][$j]['idBkn']) $isSkp2022Found = true;
        }
      }
      if ($i < $countSkpSiasn) {
        $skp = $skpFromSiasn['skp']['data'][$i];
        $idJenisJabatan = NULL;
        foreach ($daftarJenisJabatan as $jenisJabatan) {
          if ($jenisJabatan['idBkn'] == $skp['jenisJabatan']) $idJenisJabatan = $jenisJabatan['id'];
        }
        $idJenisPeraturanKinerja = NULL;
        foreach ($daftarJenisPeraturan as $jenisPeraturan) {
          if ($jenisPeraturan['idBkn'] == $skp['jenisPeraturanKinerjaKd']) $idJenisPeraturanKinerja = $jenisPeraturan['id'];
          else if ($skp['jenisPeraturanKinerjaKd'] == '') $idJenisPeraturanKinerja = 2;
        }
        if ($isSkpFound) {
          (new DataSkpController)->updateDataSkp([
            'idJenisJabatan'=>intval($idJenisJabatan),
            'tahun'=>$skp['tahun'],
            'idJenisPeraturanKinerja'=>intval($idJenisPeraturanKinerja),
            'nilaiSkp'=>$skp['nilaiSkp'],
            'orientasiPelayanan'=>$skp['orientasiPelayanan'],
            'integritas'=>$skp['integritas'],
            'komitmen'=>$skp['komitmen'],
            'disiplin'=>$skp['disiplin'],
            'kerjaSama'=>$skp['kerjasama'],
            'kepemimpinan'=>$skp['kepemimpinan'],
            'nilaiPrestasiKerja'=>$skp['nilaiPrestasiKerja'],
            'nilaiKonversi'=>$skp['konversiNilai'],
            'nilaiIntegrasi'=>$skp['nilaiIntegrasi'],
            'nilaiPerilakuKerja'=>$skp['nilaiPerilakuKerja'],
            'inisiatifKerja'=>$skp['inisiatifKerja'],
            'nilaiRataRata'=>$skp['nilairatarata'],
            'jumlah'=>$skp['jumlah'],
            'idStatusPejabatPenilai'=>$skp['statusPenilai'] == '-' || $skp['statusPenilai'] == '-' ? 2 : 1,
            'nipNrpPejabatPenilai'=>$skp['penilaiNipNrp'],
            'namaPejabatPenilai'=>$skp['penilaiNama'],
            'jabatanPejabatPenilai'=>$skp['penilaiJabatan'],
            'unitOrganisasiPejabatPenilai'=>$skp['penilaiUnorNama'],
            'golonganPejabatPenilai'=>$skp['penilaiGolongan'],
            'tmtGolonganPejabatPenilai'=>$skp['penilaiTmtGolongan'] == '' || $skp['penilaiTmtGolongan'] == null || $skp['penilaiTmtGolongan'] == '-' ? NULL : date('Y-m-d', strtotime($skp['penilaiTmtGolongan'])),
            'idStatusAtasanPejabatPenilai'=>$skp['statusAtasanPenilai'] == '-' || $skp['statusAtasanPenilai'] == '-' ? 2 : 1,
            'nipNrpAtasanPejabatPenilai'=>$skp['atasanPenilaiNipNrp'],
            'namaAtasanPejabatPenilai'=>$skp['atasanPenilaiNama'],
            'jabatanAtasanPejabatPenilai'=>$skp['atasanPenilaiJabatan'],
            'unitOrganisasiAtasanPejabatPenilai'=>$skp['atasanPenilaiUnorNama'],
            'golonganAtasanPejabatPenilai'=>$skp['atasanPenilaiGolongan'],
            'tmtGolonganAtasanPejabatPenilai'=>$skp['atasanPenilaiTmtGolongan'] == '' || $skp['atasanPenilaiTmtGolongan'] == null || $skp['atasanPenilaiTmtGolongan'] == '-' ? NULL : date('Y-m-d', strtotime($skp['atasanPenilaiTmtGolongan'])),
            'idBkn'=>$skp['id'],
            'idPegawai'=>$idPegawai,
          ]);
        } else {
          (new DataSkpController)->insertDataSkp($request, NULL, [
            'idJenisJabatan'=>intval($idJenisJabatan),
            'tahun'=>$skp['tahun'],
            'idJenisPeraturanKinerja'=>intval($idJenisPeraturanKinerja),
            'nilaiSkp'=>$skp['nilaiSkp'],
            'orientasiPelayanan'=>$skp['orientasiPelayanan'],
            'integritas'=>$skp['integritas'],
            'komitmen'=>$skp['komitmen'],
            'disiplin'=>$skp['disiplin'],
            'kerjaSama'=>$skp['kerjasama'],
            'kepemimpinan'=>$skp['kepemimpinan'],
            'nilaiPrestasiKerja'=>$skp['nilaiPrestasiKerja'],
            'nilaiKonversi'=>$skp['konversiNilai'],
            'nilaiIntegrasi'=>$skp['nilaiIntegrasi'],
            'nilaiPerilakuKerja'=>$skp['nilaiPerilakuKerja'],
            'inisiatifKerja'=>$skp['inisiatifKerja'],
            'nilaiRataRata'=>$skp['nilairatarata'],
            'jumlah'=>$skp['jumlah'],
            'idStatusPejabatPenilai'=>$skp['statusPenilai'] == '-' || $skp['statusPenilai'] == '-' ? 2 : 1,
            'nipNrpPejabatPenilai'=>$skp['penilaiNipNrp'],
            'namaPejabatPenilai'=>$skp['penilaiNama'],
            'jabatanPejabatPenilai'=>$skp['penilaiJabatan'],
            'unitOrganisasiPejabatPenilai'=>$skp['penilaiUnorNama'],
            'golonganPejabatPenilai'=>$skp['penilaiGolongan'],
            'tmtGolonganPejabatPenilai'=>$skp['penilaiTmtGolongan'] == '' || $skp['penilaiTmtGolongan'] == null || $skp['penilaiTmtGolongan'] == '-' ? NULL : date('Y-m-d', strtotime($skp['penilaiTmtGolongan'])),
            'idStatusAtasanPejabatPenilai'=>$skp['statusAtasanPenilai'] == '-' || $skp['statusAtasanPenilai'] == '-' ? 2 : 1,
            'nipNrpAtasanPejabatPenilai'=>$skp['atasanPenilaiNipNrp'],
            'namaAtasanPejabatPenilai'=>$skp['atasanPenilaiNama'],
            'jabatanAtasanPejabatPenilai'=>$skp['atasanPenilaiJabatan'],
            'unitOrganisasiAtasanPejabatPenilai'=>$skp['atasanPenilaiUnorNama'],
            'golonganAtasanPejabatPenilai'=>$skp['atasanPenilaiGolongan'],
            'tmtGolonganAtasanPejabatPenilai'=>$skp['atasanPenilaiTmtGolongan'] == '' || $skp['atasanPenilaiTmtGolongan'] == null || $skp['atasanPenilaiTmtGolongan'] == '-' ? NULL : date('Y-m-d', strtotime($skp['atasanPenilaiTmtGolongan'])),
            'idBkn'=>$skp['id'],
            'idPegawai'=>$idPegawai,
          ]);
        }
      }
      if ($i < $countSkp2022Siasn) {
        $skp2022 = $skpFromSiasn['skp2022']['data'][$i];
        $golonganPejabatPenilai = NULL;
        foreach ($daftarGolongan as $gol) {
          if ($gol['idBkn'] == $skp2022['penilaiGolonganId']) $golonganPejabatPenilai = $gol['id'];
        }
        if ($isSkp2022Found) {
          (new DataSkpController)->updateDataSkp2022([
            'id' => $skp2022['id'],
            'idPegawai' => intval($idPegawai),
            'tahun' => $skp2022['tahun'],
            'perilakuKerja' => intval($skp2022['PerilakuKerjaNilai']),
            'hasilKinerja' => intval($skp2022['hasilKinerjaNilai']),
            'kuadranKinerja' => intval($skp2022['KuadranKinerjaNilai']),
            'nipNrpPejabatPenilai' => $skp2022['nipNrpPenilai'],
            'namaPejabatPenilai' => $skp2022['namaPenilai'],
            'statusPejabatPenilai' => $skp2022['statusPenilai'] === 'NON ASN' ? 2 : 1,
            'unitOrganisasiPejabatPenilai' => $skp2022['penilaiUnorNm'],
            'jabatanPejabatPenilai' => $skp2022['penilaiJabatanNm'],
            'golonganPejabatPenilai' => $golonganPejabatPenilai,
          ]);
        } else {
          (new DataSkpController)->insertDataSkp2022($request, NULL, [
            'id' => $skp2022['id'],
            'idPegawai' => intval($idPegawai),
            'tahun' => $skp2022['tahun'],
            'perilakuKerja' => intval($skp2022['PerilakuKerjaNilai']),
            'hasilKinerja' => intval($skp2022['hasilKinerjaNilai']),
            'kuadranKinerja' => intval($skp2022['KuadranKinerjaNilai']),
            'nipNrpPejabatPenilai' => $skp2022['nipNrpPenilai'],
            'namaPejabatPenilai' => $skp2022['namaPenilai'],
            'statusPejabatPenilai' => $skp2022['statusPenilai'] === 'NON ASN' ? 2 : 1,
            'unitOrganisasiPejabatPenilai' => $skp2022['penilaiUnorNm'],
            'jabatanPejabatPenilai' => $skp2022['penilaiJabatanNm'],
            'golonganPejabatPenilai' => $golonganPejabatPenilai,
          ]);
        }
      }
    }

    $getDataSkp = (new DataSkpController)->getDataSkp($request, $idPegawai);

    $callback = [
      'message' => "Data SKP sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
      'data' => $getDataSkp['message'],
      'status' => 2
    ];
    return $callback;
  }
}
