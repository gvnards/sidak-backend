<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ApiSiasnSyncController extends ApiSiasnController
{
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
    $jabatanFromSiasn = $jabatanFromSiasn['data'];

    ///// get jabatan asn dari sidak
    $jabatanFromSidak = DB::table('m_data_jabatan')->where([
      ['idPegawai', '=', $idPegawai]
    ])->get()->toJson();
    $jabatanFromSidak = json_decode($jabatanFromSidak, true);

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
      if (count($unorSidak) > 0) {
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
        $jabatanSidak = json_decode(DB::table('m_jabatan')->where([
          ['kodeKomponen', '=', $unorSidak[0]['kodeKomponen']],
          ['idBkn', '=', $jabatanId]
        ])->get()->toJson(), true);

        // cek jabatanId ada dalam Peta Jabatan sekarang atau tidak
        $idJabatan = 0;
        if (count($jabatanSidak) === 0) {
          $idJabatan = DB::table('m_jabatan')->insertGetId([
            'id' => NULL,
            'nama' => $jabatanNama.' (Tidak ada di dalam Peta Jabatan Unit Organisasi)',
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
}
