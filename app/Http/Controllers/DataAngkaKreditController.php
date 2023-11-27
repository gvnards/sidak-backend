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
      $data = json_decode(DB::table('m_data_angka_kredit')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->where([
        ['m_data_angka_kredit.idPegawai', '=', $idPegawai],
        ['m_data_angka_kredit.idUsulanHasil', '=', 1],
        ['m_data_angka_kredit.idUsulan', '=', 1]
      ])->orderBy('m_data_angka_kredit.periodePenilaianSelesai', 'desc')->get([
        'm_data_angka_kredit.id as id',
        'm_data_angka_kredit.periodePenilaianMulai as periodeMulai',
        'm_data_angka_kredit.periodePenilaianSelesai as periodeSelesai',
        'm_data_angka_kredit.angkaKreditTotal as totalAngkaKredit',
        'm_jabatan.nama as jabatan'
      ]), true);
    } else {
      $data = json_decode(DB::table('m_data_angka_kredit')->where([
        ['id', '=', $idUsulan]
      ])->get(), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pak', 'pdf');
    }

    return $data;
  }

  public function getDataCreated(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $jenisAngkaKredit = $this->getDaftarJenisAngkaKredit();
    $jabatan = $this->getDataJabatanFungsional($idPegawai);
    $dokumenKategori = (new DokumenController)->getDocumentCategory('angka kredit');

    $callback = [
      'message' => [
        'jenisAngkaKredit' => $jenisAngkaKredit,
        'jabatan' => $jabatan,
        'dokumenKategori' => $dokumenKategori
      ],
      'status' => 2
    ];

    return $this->encrypt($username, json_encode($callback));
  }

  public function getDataUpdated(Request $request, $idPegawai, $idUsulan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

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

    return $this->encrypt($username, json_encode($callback));
  }

  public function getListDataAngkaKredit(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $data = $this->getDataAngkaKredit($idPegawai, NULL);

    $callback = [
      'message' => [
        'dataAngkaKredit' => $data
      ],
      'status' => 2
    ];

    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataAngkaKredit(Request $request, $id=NULL) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));

    $message = json_decode($this->decrypt($username, $request->message), true);
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_angka_kredit')->where([
        ['idDataAngkaKreditUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return $this->encrypt($username, json_encode([
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ]));
      }
    } else {
      // check ketika sudah ada data yg ditambahkan dan belum diapprove, return info tunggu disahkan
      $countIsAny = count(json_decode(DB::table('m_data_angka_kredit')->where([
        ['m_data_angka_kredit.idPegawai', '=', intval($message['idPegawai'])],
        ['m_data_angka_kredit.idUsulan', '=', 1],
        ['m_data_angka_kredit.idUsulanHasil', '=', 3]
      ])->get()));
      if ($countIsAny > 0) {
        return $this->encrypt($username, json_encode([
          'message' => "Maaf, Data Angka Kredit sudah ada yang ditambahkan tetapi belum diverifikasi.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ]));
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
    return $this->encrypt($username, json_encode($callback));
  }
}
