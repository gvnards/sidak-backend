<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataAngkaKreditController extends Controller
{
  public function getDataJabatanFungsional($idPegawai) {
    $data = json_decode(DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
      ['m_data_jabatan.idPegawai', '=', $idPegawai],
      ['m_data_jabatan.idBkn', '!=', ''],
      ['m_data_jabatan.idBkn', '!=', null],
      ['m_jabatan.idJenisJabatan', '=', 2],
    ])->orderBy('m_data_jabatan.tmt', 'desc')->get([
      'm_data_jabatan.id as idDataJabatan',
      'm_jabatan.nama as jabatan'
    ]), true);

    $data_ = [];
    for($i = 0; $i < count($data); $i++) {
      $isFind = false;
      for($j = 0; $j < count($data_); $j++) {
        if ($data_[$j]['jabatan'] === $data[$i]['jabatan']) {
          $isFind = true;
          $j = count($data);
        }
      }
      if (!$isFind) {
        array_push($data_, $data[$i]);
      }
    }

    return $data_;
  }

  public function getDaftarJenisAngkaKredit() {
    $data = json_decode(DB::table('m_daftar_jenis_angka_kredit')->get(), true);

    return $data;
  }

  public function getDataAngkaKredit($idPegawai, $idUsulan=NULL) {
    if ($idUsulan === NULL) {
      $data = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->leftJoin('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->leftJoin('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
        ['m_data_angka_kredit.idPegawai', '=', $idPegawai],
        ['m_data_angka_kredit.idUsulanHasil', '=', 1],
        ['m_data_angka_kredit.idUsulan', '=', 1]
      ])->orderBy('m_data_angka_kredit.periodePenilaianSelesai', 'desc')->get([
        'm_data_angka_kredit.id as id',
        'm_daftar_jenis_angka_kredit.jenisAngkaKredit as jenisPAK',
        'm_data_angka_kredit.periodePenilaianMulai as periodeMulai',
        'm_data_angka_kredit.periodePenilaianSelesai as periodeSelesai',
        'm_data_angka_kredit.angkaKreditTotal as totalAngkaKredit',
        'm_jabatan.nama as jabatan'
      ]), true);
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['jabatan'] === '' || $data[$i]['jabatan'] === NULL) {
          $data[$i]['jabatan'] = '(Silahkan Update PAK Anda)';
        }
        $data[$i]['jenisPAK'] = $data[$i]['jenisPAK'] == null ? "Konvensional" : substr($data[$i]['jenisPAK'], 0, strpos($data[$i]['jenisPAK']," ("));
        $data[$i]['jabatan'] =  "(".$data[$i]['jenisPAK'].") | ".$data[$i]['jabatan'];
      }
    } else {
      $data = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->where([
        ['m_data_angka_kredit.id', '=', $idUsulan]
      ])->get([
        'm_data_angka_kredit.*',
        'm_data_jabatan.id AS idJbtn'
      ]), true);
      if ($data[0]['idJbtn'] === null) {
        $data[0]['idDataJabatan'] = 0;
      }
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pak', 'pdf');
      $data[0]['dokumen'] = '';
    }

    return $data;
  }

  private function hasIntegrasi($idPegawai) {
    $integrasi = json_decode(DB::table('m_data_angka_kredit')->where([
      ['idPegawai', '=', $idPegawai],
      ['idDaftarJenisAngkaKredit', '=', 2],
      ['idUsulan', '=', 1],
      ['idUsulanHasil', '=', 1]
    ])->get(), true);
    return count($integrasi) > 0;
  }

  public function getDataCreated(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $jenisAngkaKredit = $this->getDaftarJenisAngkaKredit();
    $jabatan = $this->getDataJabatanFungsional($idPegawai);
    $dokumenKategori = (new DokumenController)->getDocumentCategory('angka kredit');
    $hasIntegrasi = $this->hasIntegrasi($idPegawai);

    $callback = [
      'message' => [
        'hasIntegrasi' => $hasIntegrasi,
        'jenisAngkaKredit' => $jenisAngkaKredit,
        'jabatan' => $jabatan,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function getDataUpdated(Request $request, $idPegawai, $idUsulan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $jabatan = $this->getDataJabatanFungsional($idPegawai);
    $jenisAngkaKredit = $this->getDaftarJenisAngkaKredit();
    $dataAngkaKredit = $this->getDataAngkaKredit($idPegawai, $idUsulan);
    $dokumenKategori = (new DokumenController)->getDocumentCategory('angka kredit');

    $callback = [
      'message' => [
        'jenisAngkaKredit' => $jenisAngkaKredit,
        'jabatan' => $jabatan,
        'dataAngkaKredit' => $dataAngkaKredit,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function getListDataAngkaKredit(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $data = $this->getDataAngkaKredit($idPegawai, NULL);

    $callback = [
      'message' => [
        'dataAngkaKredit' => $data
      ],
      'status' => 2
    ];

    return $callback;
  }

  public function insertDataAngkaKredit(Request $request, $id=NULL) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $message = json_decode($this->decrypt($username, $request->message), true);
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_angka_kredit')->where([
        ['idDataAngkaKreditUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    } else {
      // check ketika sudah ada data yg ditambahkan dan belum diapprove, return info tunggu disahkan
      $countIsAny = count(json_decode(DB::table('m_data_angka_kredit')->where([
        ['m_data_angka_kredit.idPegawai', '=', intval($message['idPegawai'])],
        ['m_data_angka_kredit.idUsulan', '=', 1],
        ['m_data_angka_kredit.idUsulanHasil', '=', 3]
      ])->get()));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, Data Angka Kredit sudah ada yang ditambahkan tetapi belum diverifikasi.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    }

    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_SK_PAK_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SK_PAK_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'pak');

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

    $data = DB::table('m_data_angka_kredit')->insert([
      'id' => NULL,
      'idDaftarJenisAngkaKredit' => $message['idDaftarJenisAngkaKredit'],
      'idDataJabatan' => $message['idDataJabatan'],
      'tahun' => $tahun,
      'periodePenilaianMulai' => $message['periodePenilaianMulai'],
      'periodePenilaianSelesai' => $message['periodePenilaianSelesai'],
      'angkaKreditUtama' => $angkaKreditUtama,
      'angkaKreditPenunjang' => $angkaKreditPenunjang,
      'angkaKreditTotal' => $message['angkaKreditTotal'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataAngkaKreditUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    // kondisi bypass
    $isByPass = $this->isUsernameGetByPass($username);
    if ($isByPass) {
      $dt = json_decode(DB::table('m_data_angka_kredit')->where([
        'idDokumen' => $dokumen,
        'idPegawai' => $message['idPegawai'],
        'idUsulan' => $id == NULL ? 1 : 2,
        'idUsulanStatus' => 1,
        'idUsulanHasil' => 3,
      ])->get(), true);
      $dtUpdate = $this->updateDataAngkaKredit($dt[0]['id'], [
      'idUsulanStatus' => 3,
      'idUsulanHasil' => 1,
      'keteranganUsulan' => ''
      ], $isByPass);
      if ($dtUpdate['status'] === 4) {
        return [
          'message' => 'Data sudah diverifikasi oleh admin. Silahkan refresh atau verifikasi yang data lain.',
          'status' => 3
        ];
      }
      $callback = $dtUpdate;
    }
    return $callback;
  }

  public function updateDataAngkaKredit($idUsulan, $message, $isByPass=false) {
    $usulan = json_decode(DB::table('m_data_angka_kredit')->where([
      ['id', '=', $idUsulan]
    ])->get()->toJson(), true)[0];
    if (intval($usulan['idUsulanStatus']) !== 1) {
      /// data sudah diverifikasi
      return [
        'status' => 4
      ];
    }
    if (intval($message['idUsulanHasil']) == 1) {
      $response = $response = (new ApiSiasnSyncController)->insertRiwayatAngkaKredit($idUsulan);
      if (!$response['success']) {
        $callback = [
          'message' => $response['message'],
          'status' => 3
        ];
        if ($isByPass) {
          /// delete ketika ada masalah
          DB::table('m_data_angka_kredit')->where([
            ['id', '=', $idUsulan]
          ])->delete();
        }
        return $callback;
      } else {
        DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
          'idBkn' => $response['mapData']['rwAngkaKreditId'],
        ]);
        $dokumen = json_decode(DB::table('m_dokumen')->where([
          ['id', '=', $usulan['idDokumen']]
        ])->get()->toJson(), true)[0];
        (new ApiSiasnController)->insertDokumenRiwayat($response['mapData']['rwAngkaKreditId'], 879, 'pak', $dokumen['nama'], 'pdf');
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
        $tahun = $newData[0]['tahun'];
        $angkaKreditUtama = $newData[0]['angkaKreditUtama'];
        $angkaKreditPenunjang = $newData[0]['angkaKreditPenunjang'];
        switch (intval($newData[0]['idDaftarJenisAngkaKredit'])) {
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
          $this->deleteDokumen($oldData['idDokumen'], 'pak', 'pdf');
        }
      } else {
        $getData = $newData[0];
        DB::table('m_data_angka_kredit')->where('id', '=', $idUsulan)->update([
          'idDokumen' => 1
        ]);
        $this->deleteDokumen($getData['idDokumen'], 'pak', 'pdf');
      }
    }
    return [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
  }

  public function deleteDataJabatan(Request $request, $idDataAngkaKredit) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $dataAngkaKredit = json_decode(DB::table('m_data_angka_kredit')->where([
      ['id', '=', $idDataAngkaKredit]
    ])->get(), true);
    if (count($dataAngkaKredit) === 0) {
      return [
        'message' => 'Data Angka Kredit tidak ditemukan.',
        'status' => 3
      ];
    }
    $deleteAngkaKredit = (new ApiSiasnController)->deleteRiwayatAngkaKredit($dataAngkaKredit[0]['idBkn']);
    return [
      'message' => ($deleteAngkaKredit['success'] || $deleteAngkaKredit['message'] === 'success') ? 'Data Angka Kredit berhasil dihapus.' : 'Data Angka Kredit gagal dihapus.',
      'status' => ($deleteAngkaKredit['success'] || $deleteAngkaKredit['message'] === "success") ? 2 : 3
    ];
  }
}
