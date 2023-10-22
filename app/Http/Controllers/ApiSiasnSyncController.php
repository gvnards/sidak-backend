<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ApiSiasnSyncController extends ApiSiasnController
{

  public function syncAngkaKreditASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $getAsn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get(), true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    $angkaKreditFromSiasn = $this->getRiwayatAngkaKreditASN($request, $nipBaru);

    if (!isset($angkaKreditFromSiasn['data'])) {
      return $this->encrypt($username, json_encode([
        'message' => "Data Angka Kredit tidak dapat ditarik dari MySAPK.\nMasalah ini sedang ditangani oleh BKN.",
        'status' => 3
      ]));
    } else if (gettype($angkaKreditFromSiasn['data']) != "array") {
      return $this->encrypt($username, json_encode([
        'message' => "Data angka kredit sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
        'status' => 2
      ]));
    }
    $angkaKreditFromSiasn = $angkaKreditFromSiasn['data'];

    $angkaKreditFromSidak = json_decode(DB::table('m_data_angka_kredit')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get(), true);

    ///// cek apakah angka kredit dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    for($i = 0; $i < count($angkaKreditFromSidak); $i++) {
      if ($angkaKreditFromSidak[$i]['idBkn'] != '' && $angkaKreditFromSidak[$i]['idBkn'] != null) {
        $isFind = false;
        for($j=0; $j<count($angkaKreditFromSiasn); $j++) {
          if ($angkaKreditFromSidak[$i]['idBkn'] == $angkaKreditFromSiasn[$j]['id']) {
            $isFind = true;
          }
        }
        if (!$isFind) {
          DB::table('m_data_angka_kredit')->where([
            ['id', '=', $angkaKreditFromSidak[$i]['id']]
          ])->delete();
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newAngkaKreditFromSiasn = [];
    for($i=0; $i<count($angkaKreditFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($angkaKreditFromSidak); $j++) {
        // cek
        if ($angkaKreditFromSiasn[$i]['id'] == $angkaKreditFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newAngkaKreditFromSiasn, $angkaKreditFromSiasn[$i]);
      }
    }

    // insert to database
    $affected = '';
    for($i = 0; $i < count($newAngkaKreditFromSiasn); $i++) {
      if ($newAngkaKreditFromSiasn[$i]['rwJabatan'] !== '' || $newAngkaKreditFromSiasn[$i]['rwJabatan'] !== null) {
        $dataJabatanSidak = json_decode(DB::table('m_data_jabatan')->where([
          ['idBkn', '=', $newAngkaKreditFromSiasn[$i]['rwJabatan']]
        ])->get(), true);
        if (count($dataJabatanSidak) > 0) {
          $idDaftarJenisAngkaKredit = NULL;
          if ($newAngkaKreditFromSiasn[$i]['isAngkaKreditPertama'] == '1') $idDaftarJenisAngkaKredit = 1;
          else if ($newAngkaKreditFromSiasn[$i]['isIntegrasi'] == '1') $idDaftarJenisAngkaKredit = 2;
          else if ($newAngkaKreditFromSiasn[$i]['isKonversi'] == '1') $idDaftarJenisAngkaKredit = 3;
          DB::table('m_data_angka_kredit')->insert([
            'id' => NULL,
            'idDaftarJenisAngkaKredit' => $idDaftarJenisAngkaKredit,
            'idDataJabatan' => $dataJabatanSidak[0]['id'],
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
    }

    $callback = [
      'message' => "Data angka kredit sudah berhasil disinkronisasi dari MySAPK.\nJika setelah sinkronisasi tidak ada angka kredit yang muncul, silahkan tambahkan angka kredit sesuai dengan dasar Sertifikat yang dimiliki.\n Dan jika terdapat ketidaksesuaian angka kredit, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function syncJabatanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $jabatanFromSiasn = $this->getRiwayatJabatanASN($request, $nipBaru);
    if (!isset($jabatanFromSiasn['data'])) {
      return $this->encrypt($username, json_encode([
        'message' => "Data Jabatan tidak dapat ditarik dari MySAPK.\nMasalah ini sedang ditangani oleh BKN.",
        'status' => 3
      ]));
    } else if (gettype($jabatanFromSiasn['data']) != "array") {
      return $this->encrypt($username, json_encode([
        'message' => "Data Jabatan tidak dapat ditarik dari MySAPK.\nMasalah ini sedang ditangani oleh BKN.",
        'status' => 3
      ]));
    }
    $jabatanFromSiasn = $jabatanFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $jabatanFromSidak = DB::table('m_data_jabatan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $jabatanFromSidak = json_decode($jabatanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
    for($i=0; $i<count($jabatanFromSidak); $i++) {
      if ($jabatanFromSidak[$i]['idBkn'] != '' && $jabatanFromSidak[$i]['idBkn'] != null) {
        $isFind = false;
        for($j=0; $j<count($jabatanFromSiasn); $j++) {
          if ($jabatanFromSidak[$i]['idBkn'] == $jabatanFromSiasn[$j]['id']) {
            $isFind = true;
          }
        }
        if (!$isFind) {
          DB::table('m_data_jabatan')->where([
            ['id', '=', $jabatanFromSidak[$i]['id']]
          ])->delete();
        }
      }
    }

    ///// cek apakah jabatan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newJabatanFromSiasn
    $newJabatanFromSiasn = [];
    for($i=0; $i<count($jabatanFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($jabatanFromSidak); $j++) {
        // cek
        if ($jabatanFromSiasn[$i]['id'] == $jabatanFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind) {
        array_push($newJabatanFromSiasn, $jabatanFromSiasn[$i]);
      }
    }

    ///// loop insert jabatan dari siasn, tetapi cek terlebih dahulu, idUnor dan idJabatan ada tidak dalam peta jabatan saat ini
    $affected = '';
    for($i=0; $i<count($newJabatanFromSiasn); $i++) {
      $unorSidak = json_decode(DB::table('m_unit_organisasi')->where([
        ['idBkn', '=', $newJabatanFromSiasn[$i]['unorId']]
      ])->get()->toJson(), true);
      // cek unorId ada dalam SOTK sekarang atau tidak
      if (count($unorSidak) === 0) {
        $newId = DB::table('m_unit_organisasi')->insertGetId([
          'id' => NULL,
          'nama' => $newJabatanFromSiasn[$i]['unorNama'],
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
          'kodeKomponen' => '-'.$newId
        ]);
        $unorSidak = json_decode(DB::table('m_unit_organisasi')->where([
          ['idBkn', '=', $newJabatanFromSiasn[$i]['unorId']]
        ])->get()->toJson(), true);
      }
      else {
        $jabatanId = '';
        switch (intval($newJabatanFromSiasn[$i]['jenisJabatan'])) {
          case 1:
            $idJenisJabatan = 1;
            $jabatanId = $newJabatanFromSiasn[$i]['unorId'];
            $jabatanNama = $newJabatanFromSiasn[$i]['namaJabatan'] ?? 'Kepala '.$newJabatanFromSiasn[$i]['unorNama'];
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
    }

    $callback = [
      'message' => "Data jabatan sudah berhasil disinkronisasi dari MySAPK.\nData yang dapat disinkronisasi adalah data sesuai dengan SOTK dan Peta Jabatan saat ini.\nJika setelah sinkronisasi tidak ada jabatan yang muncul, silahkan tambahkan jabatan sesuai dengan dasar SK Jabatan Definitif Terakhir.\n Dan jika terdapat ketidaksesuaian jabatan, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function syncJabatanASNAll(Request $request, $from, $to) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $allPegawai = json_decode(DB::table('m_pegawai')->get()->toJson(), true);
    for($i=($from-1); $i<($to-1); $i++) {
      $this->syncJabatanASN($request, $allPegawai[$i]['id']);
    }
    return $this->encrypt($username, json_encode([
      'message' => "Sinkron ".($to-$from+1)." pegawai telah berhasil.",
      'status' => 2
    ]));
  }
  public function insertRiwayatJabatan(Request $request, $idUsulan) {
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
    $response = $this->insertRiwayatJabatanASN($request, $data);
    return $response;
    // return $data;
  }
  public function syncDiklatASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
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

    $callback = [
      'message' => "Data diklat/kursus sudah berhasil disinkronisasi dari MySAPK.\nJika setelah sinkronisasi tidak ada diklat/kursus yang muncul, silahkan tambahkan diklat/kursus sesuai dengan dasar Sertifikat yang dimiliki.\n Dan jika terdapat ketidaksesuaian diklat/kursus, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function insertRiwayatDiklatKursus(Request $request, $idUsulan) {
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
      $response = $this->insertRiwayatDiklatASN($request, $data);
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
      $response = $this->insertRiwayatKursusASN($request, $data);
    }
    return $response;
  }
  public function syncPangkatGolonganASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $pangkatGolonganFromSiasn = $this->getRiwayatPangkatGolonganASN($request, $nipBaru);
    $pangkatGolonganFromSiasn = $pangkatGolonganFromSiasn['data'];

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

    $callback = [
      'message' => "Data pangkat/golongan sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian pangkat/golongan, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function syncPendidikanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat pendidikan dari siasn
    $pendidikanFromSiasn = $this->getRiwayatPendidikanASN($request, $nipBaru);
    $pendidikanFromSiasn = $pendidikanFromSiasn['data'];

    ///// get pendidikan asn dari sidak
    $pendidikanFromSidak = DB::table('m_data_pendidikan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $pendidikanFromSidak = json_decode($pendidikanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
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
            ['id', '=', $pendidikanFromSidak[$i]['id']]
          ])->delete();
        }
      }
    }

    ///// cek apakah pendidikan yang dari siasn sudah ada di sidak, jika belum, kumpulkan ke dalam variabel newPendidikanFromSiasn
    $newPendidikanFromSiasn = [];
    for($i=0; $i<count($pendidikanFromSiasn); $i++) {
      $isFind = false;
      for($j=0; $j<count($pendidikanFromSidak); $j++) {
        // cek
        if ($pendidikanFromSiasn[$i]['id'] == $pendidikanFromSidak[$j]['idBkn']) {
          $isFind = true;
        }
      }
      if (!$isFind/* && $pendidikanFromSiasn[$i]['namaSekolah'] != null && $pendidikanFromSiasn[$i]['nomorIjasah'] != null*/) {
        array_push($newPendidikanFromSiasn, $pendidikanFromSiasn[$i]);
      }
    }

    ///// loop cek data pendidikan pertama kali saat diangkat pns
    $pendidikanPertamaSaatPns = null;
    for($i=0; $i<count($newPendidikanFromSiasn); $i++) {
      if (intval($newPendidikanFromSiasn[$i]['isPendidikanPertama']) == 1) {
        $pendidikanPertamaSaatPns = $newPendidikanFromSiasn[$i]['tkPendidikanId'];
      }
    }

    ///// loop insert pangkat/golongan dari siasn
    $affected = '';
    for($i=0; $i<count($newPendidikanFromSiasn); $i++) {
      $idJenisPendidikan = 1;
      if ($pendidikanPertamaSaatPns != null) {
        if (intval($pendidikanPertamaSaatPns) <= intval($newPendidikanFromSiasn[$i]['tkPendidikanId'])) {
          $idJenisPendidikan = 3;
        }
      }
      $idTingkatPendidikan = json_decode(DB::table('m_tingkat_pendidikan')->where([
        ['idBkn', '=', $newPendidikanFromSiasn[$i]['tkPendidikanId']]
      ])->get()->toJson(), true)[0];
      $idDaftarPendidikan = json_decode(DB::table('m_daftar_pendidikan')->where([
        ['idBkn', '=', $newPendidikanFromSiasn[$i]['pendidikanId']]
      ])->get()->toJson(), true)[0];
      DB::table('m_data_pendidikan')->insert([
        'id' => NULL,
        'idJenisPendidikan' => intval($newPendidikanFromSiasn[$i]['isPendidikanPertama']) > 0 ? 2 : $idJenisPendidikan,
        'idTingkatPendidikan' => $idTingkatPendidikan['id'],
        'idDaftarPendidikan' => $idDaftarPendidikan['id'],
        'namaSekolah' => $newPendidikanFromSiasn[$i]['namaSekolah'] ?? '',
        'gelarDepan' => $newPendidikanFromSiasn[$i]['gelarDepan'] ?? '',
        'gelarBelakang' => $newPendidikanFromSiasn[$i]['gelarBelakang'] ?? '',
        'tanggalLulus' => $newPendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($newPendidikanFromSiasn[$i]['tglLulus'])),
        'tahunLulus' => $newPendidikanFromSiasn[$i]['tahunLulus'] ?? 1111,
        'nomorDokumen' => $newPendidikanFromSiasn[$i]['nomorIjasah'] ?? '',
        'tanggalDokumen' => $newPendidikanFromSiasn[$i]['tglLulus'] == null ? '0000-00-00' : date('Y-m-d', strtotime($newPendidikanFromSiasn[$i]['tglLulus'])),
        'idDokumen' => NULL,
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

    $callback = [
      'message' => "Data pendidikan sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian pendidikan, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function syncHukdisASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $hukdisFromSiasn = $this->getRiwayatHukdisASN($request, $nipBaru);
    $hukdisFromSiasn = $hukdisFromSiasn['data'] ?? [];

    ///// get jabatan asn dari sidak
    $hukdisFromSidak = DB::table('m_data_hukuman_disiplin')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $hukdisFromSidak = json_decode($hukdisFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
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
            ['id', '=', $hukdisFromSidak[$i]['id']]
          ])->delete();
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
      switch ($newHukdisFromSiasn[$i]['jenisHukuman']) {
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
      $idDaftarDasarHukumHukdis = json_decode(DB::table('m_daftar_dasar_hukum_hukuman_disiplin')->where([
        ['idBkn', '=', $newHukdisFromSiasn[$i]['nomorPp']]
      ])->get(), true)[0];
      $idJenisHukuman = json_decode(DB::table('m_jenis_hukuman_disiplin')->where('idBkn', '=', $newHukdisFromSiasn[$i]['jenisHukuman'])->get(), true)[0];
      $idDaftarAlasanHukdis = json_decode(DB::table('m_daftar_alasan_hukuman_disiplin')->where('idBkn', '=', $newHukdisFromSiasn[$i]['alasanHukumanDisiplin'])->get(), true)[0];
      DB::table('m_data_hukuman_disiplin')->insert([
        'id' => NULL,
        'idJenisHukumanDisiplin' => $idJenisHukuman['id'],
        'idDaftarHukumanDisiplin' => $newHukdisFromSiasn[$i]['jenisTingkatHukumanId'] === '' ? $idDaftarHukumanDisiplin : $newHukdisFromSiasn[$i]['jenisTingkatHukumanId'],
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

    $callback = [
      'message' => "Data hukuman disiplin sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function syncPenghargaanASN(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    ///// get data asn untuk mendapatkan nip berdasarkan idPegawai
    $getAsn = DB::table('m_pegawai')->where([
      ['id', '=', $idPegawai]
    ])->get()->toJson();
    $getAsn = json_decode($getAsn, true);
    if (count($getAsn) === 0) {
      return $this->encrypt($username, json_encode([
        'message' => 'Data ASN tidak ditemukan.',
        'status' => 3
      ]));
    }
    $nipBaru = $getAsn[0]['nip'];

    ///// get data riwayat jabatan dari siasn
    $penghargaanFromSiasn = $this->getRiwayatPenghargaanASN($request, $nipBaru);
    $penghargaanFromSiasn = $penghargaanFromSiasn['data'] == 'Data tidak ditemukan' ? [] : $penghargaanFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $penghargaanFromSidak = DB::table('m_data_penghargaan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $penghargaanFromSidak = json_decode($penghargaanFromSidak, true);

    ///// cek apakah jabatan dari sidak (yang ada idBkn nya), itu masih ada atau tidak di siasn, jika tidak, hapus
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
            ['id', '=', $penghargaanFromSidak[$i]['id']]
          ])->delete();
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

    $callback = [
      'message' => "Data hukuman disiplin sudah berhasil disinkronisasi dari MySAPK.\nJika terdapat ketidaksesuaian data, dapat menghubungi Admin BKPSDM.",
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function insertRiwayatPenghargaan(Request $request, $idUsulan) {
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
    $response = $this->insertRiwayatPenghargaanASN($request, $data);
    return $response;
  }
  public function insertRiwayatAngkaKredit(Request $request, $idUsulan) {
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
    $response = $this->insertRiwayatAngkaKreditASN($request, $dataToSiasn);
    return $response;
  }
}
