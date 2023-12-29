<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSkpController extends Controller
{
  public function getDataSkp(Request $request, $idPegawai, $tahun=null, $idDataSkp=null) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if($idDataSkp === null) {
      $dataSkp2022 = json_decode(DB::table('m_data_skp_2022')->join('m_daftar_nilai_kuadran', 'm_data_skp_2022.idKuadranKinerja', '=', 'm_daftar_nilai_kuadran.id')->where([
        ['m_data_skp_2022.idPegawai', '=', $idPegawai],
        ['m_data_skp_2022.idUsulanHasil', '=', 1],
        ['m_data_skp_2022.idUsulan', '=', 1],
      ])->orderByDesc('m_data_skp_2022.tahun')->get([
        'm_data_skp_2022.id as id',
        'm_data_skp_2022.tahun as tahun',
        'm_daftar_nilai_kuadran.nama as nilai'
      ]),true);
      $dataSkp = json_decode(DB::table('m_data_skp')->where([
        ['m_data_skp.idPegawai', '=', $idPegawai],
        ['m_data_skp.idUsulanHasil', '=', 1],
        ['m_data_skp.idUsulan', '=', 1],
      ])->orderByDesc('m_data_skp.tahun')->get([
        'm_data_skp.id as id',
        'm_data_skp.tahun as tahun',
        'm_data_skp.nilaiPrestasiKerja as nilai'
      ]),true);
      $data = array_merge($dataSkp2022, $dataSkp);
      return [
        'message' => $data,
        'status' => 2
      ];
    }

    $tahun_ = intval($tahun);
    if ($tahun_ === 2022) {
      $data = json_decode(DB::table('m_data_skp_2022')->leftJoin('m_daftar_nilai_kerja_kinerja as perilakuKerja', 'm_data_skp_2022.idPerilakuKerja', '=', 'perilakuKerja.id')->leftJoin('m_daftar_nilai_kerja_kinerja as hasilKinerja', 'm_data_skp_2022.idHasilKinerja', '=', 'hasilKinerja.id')->leftJoin('m_daftar_nilai_kuadran', 'm_data_skp_2022.idKuadranKinerja', '=', 'm_daftar_nilai_kuadran.id')->leftJoin('m_status_pejabat_atasan_penilai', 'm_data_skp_2022.idStatusPejabatPenilai', '=', 'm_status_pejabat_atasan_penilai.id')->where([
        ['m_data_skp_2022.id', '=', $idDataSkp]
      ])->get([
        'm_data_skp_2022.*',
        'perilakuKerja.nama as perilakuKerja',
        'hasilKinerja.nama as hasilKinerja',
        'm_daftar_nilai_kuadran.nama as kuadranKinerja',
        'm_status_pejabat_atasan_penilai.nama as statusPejabatPenilai'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'skp', 'pdf');
      return $data;
    } else {
      $data = json_decode(DB::table('m_data_skp')->leftJoin('m_dokumen', 'm_data_skp.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_skp.id', '=', $idDataSkp],
      ])->get(), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'skp', 'pdf');
      return $data;
    }
  }

  public function getDataSkpDetail(Request $request, $idPegawai, $tahun, $idDataSkp) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $dataSkp = $this->getDataSkp($request, $idPegawai, $tahun, $idDataSkp);
    $dokumenKategori = (new DokumenController)->getDocumentCategory('skp');
    $message = [
      'dataSkp' => $dataSkp,
      'dokumenKategori' => $dokumenKategori,
    ];
    if (intval($tahun) < 2022) {
      $daftarJenisJabatan = $this->getJenisJabatan();
      $daftarJenisPeraturanKinerja = $this->getJenisPeraturanKinerja();
      $daftarStatus = $this->getJenisStatusPejabatAtasanPenilai();
      $message['daftarJenisJabatan'] = $daftarJenisJabatan;
      $message['daftarJenisPeraturanKinerja'] = $daftarJenisPeraturanKinerja;
      $message['daftarStatus'] = $daftarStatus;
    }

    $callback = [
      'message' => $message,
      'status' => 2
    ];

    return $callback;
  }

  private function getJenisJabatan() {
    $data = json_decode(DB::table('m_jenis_jabatan')->get(), true);
    return $data;
  }

  private function getJenisPeraturanKinerja() {
    $data = json_decode(DB::table('m_jenis_peraturan_kinerja')->get(), true);
    return $data;
  }

  private function getJenisStatusPejabatAtasanPenilai() {
    $data = json_decode(DB::table('m_status_pejabat_atasan_penilai')->get(), true);
    return $data;
  }

  public function updateDokumenSkp(Request $request, $tahun, $idDataSkp) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $username = $this->isAuth($request)['username'];
    $message = json_decode($this->decrypt($username, $request->message), true);
    $tahun_ = intval($tahun);
    $dataSkp = json_decode(DB::table($tahun_ === 2022 ? 'm_data_skp_2022' : 'm_data_skp')->where([
      ['id', '=', $idDataSkp]
    ])->get(), true);
    $asn = json_decode(DB::table('m_pegawai')->where([
      ['id', '=', $dataSkp[0]['idPegawai']]
    ])->get(), true);
    DB::table($tahun_ === 2022 ? 'm_data_skp_2022' : 'm_data_skp')->where([
      ['id', '=', $idDataSkp]
    ])->update([
      'idDokumen' => NULL,
    ]);
    $this->deleteDokumen($dataSkp[0]['idDokumen'], 'skp', 'pdf');
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => 'DOK_SKP_'.$tahun.'_'.$asn[0]['nip'].'_'.$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen('DOK_SKP_'.$tahun.'_'.$asn[0]['nip'].'_'.$message['date'], $message['dokumen'], 'pdf', 'skp');
    DB::table($tahun_ === 2022 ? 'm_data_skp_2022' : 'm_data_skp')->where([
      ['id', '=', $idDataSkp]
    ])->update([
      'idDokumen' => $dokumen,
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    return [
      'message' => 'Dokumen SKP telah berhasil diupload.',
      'status' => 2
    ];
  }

  public function insertDataSkp(Request $request, $id=NULL, $dt=NULL) {
    if ($dt !== NULL) {
      DB::table('m_data_skp')->insert([
        'id'=>NULL,
        'idJenisJabatan'=>$dt['idJenisJabatan'],
        'tahun'=>$dt['tahun'],
        'idJenisPeraturanKinerja'=>$dt['idJenisPeraturanKinerja'],
        'nilaiSkp'=>$dt['nilaiSkp'],
        'orientasiPelayanan'=>$dt['orientasiPelayanan'],
        'integritas'=>$dt['integritas'],
        'komitmen'=>$dt['komitmen'],
        'disiplin'=>$dt['disiplin'],
        'kerjaSama'=>$dt['kerjaSama'],
        'kepemimpinan'=>$dt['kepemimpinan'],
        'nilaiPrestasiKerja'=>$dt['nilaiPrestasiKerja'],
        'nilaiKonversi'=>$dt['nilaiKonversi'],
        'nilaiIntegrasi'=>$dt['nilaiIntegrasi'],
        'nilaiPerilakuKerja'=>$dt['nilaiPerilakuKerja'],
        'inisiatifKerja'=>$dt['inisiatifKerja'],
        'nilaiRataRata'=>$dt['nilaiRataRata'],
        'jumlah'=>$dt['jumlah'],
        'idStatusPejabatPenilai'=>$dt['idStatusPejabatPenilai'],
        'nipNrpPejabatPenilai'=>$dt['nipNrpPejabatPenilai'],
        'namaPejabatPenilai'=>$dt['namaPejabatPenilai'],
        'jabatanPejabatPenilai'=>$dt['jabatanPejabatPenilai'],
        'unitOrganisasiPejabatPenilai'=>$dt['unitOrganisasiPejabatPenilai'],
        'golonganPejabatPenilai'=>$dt['golonganPejabatPenilai'],
        'tmtGolonganPejabatPenilai'=>$dt['tmtGolonganPejabatPenilai'],
        'idStatusAtasanPejabatPenilai'=>$dt['idStatusAtasanPejabatPenilai'],
        'nipNrpAtasanPejabatPenilai'=>$dt['nipNrpAtasanPejabatPenilai'],
        'namaAtasanPejabatPenilai'=>$dt['namaAtasanPejabatPenilai'],
        'jabatanAtasanPejabatPenilai'=>$dt['jabatanAtasanPejabatPenilai'],
        'unitOrganisasiAtasanPejabatPenilai'=>$dt['unitOrganisasiAtasanPejabatPenilai'],
        'golonganAtasanPejabatPenilai'=>$dt['golonganAtasanPejabatPenilai'],
        'tmtGolonganAtasanPejabatPenilai'=>$dt['tmtGolonganAtasanPejabatPenilai'],
        'idBkn'=>$dt['idBkn'],
        'idPegawai'=>$dt['idPegawai'],
        'idDokumen'=>NULL,
        'idUsulan'=>1,
        'idUsulanStatus'=>4,
        'idUsulanHasil'=>1,
        'idDataSkpUpdate'=>NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
      return;
    }
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_skp')->where([
        ['idDataSkpUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return $this->encrypt($username, json_encode([
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ]));
      }
    }
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_SKP_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SKP_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'skp');

    $data = DB::table('m_data_skp')->insert([
      'id' => NULL,
      'idJenisJabatan' => $message['idJenisJabatan'],
      'tahun' => $message['tahun'],
      'idJenisPeraturanKinerja' => $message['idJenisPeraturanKinerja'],
      'nilaiSkp' => $message['nilaiSkp'],
      'orientasiPelayanan' => $message['orientasiPelayanan'],
      'integritas' => $message['integritas'],
      'komitmen' => $message['komitmen'],
      'disiplin' => $message['disiplin'],
      'kerjaSama' => $message['kerjaSama'],
      'kepemimpinan' => $message['kepemimpinan'],
      'nilaiPrestasiKerja' => $message['nilaiPrestasiKerja'],
      'nilaiKonversi' => $message['nilaiKonversi'],
      'nilaiIntegrasi' => $message['nilaiIntegrasi'],
      'nilaiPerilakuKerja' => $message['nilaiPerilakuKerja'],
      'inisiatifKerja' => $message['inisiatifKerja'],
      'nilaiRataRata' => $message['nilaiRataRata'],
      'jumlah' => $message['jumlah'],
      'idStatusPejabatPenilai' => $message['idStatusPejabatPenilai'],
      'nipNrpPejabatPenilai' => $message['nipNrpPejabatPenilai'],
      'namaPejabatPenilai' => $message['namaPejabatPenilai'],
      'jabatanPejabatPenilai' => $message['jabatanPejabatPenilai'],
      'unitOrganisasiPejabatPenilai' => $message['unitOrganisasiPejabatPenilai'],
      'golonganPejabatPenilai' => $message['golonganPejabatPenilai'],
      'tmtGolonganPejabatPenilai' => $message['tmtGolonganPejabatPenilai'],
      'idStatusAtasanPejabatPenilai' => $message['idStatusAtasanPejabatPenilai'],
      'nipNrpAtasanPejabatPenilai' => $message['nipNrpAtasanPejabatPenilai'],
      'namaAtasanPejabatPenilai' => $message['namaAtasanPejabatPenilai'],
      'jabatanAtasanPejabatPenilai' => $message['jabatanAtasanPejabatPenilai'],
      'unitOrganisasiAtasanPejabatPenilai' => $message['unitOrganisasiAtasanPejabatPenilai'],
      'golonganAtasanPejabatPenilai' => $message['golonganAtasanPejabatPenilai'],
      'tmtGolonganAtasanPejabatPenilai' => $message['tmtGolonganAtasanPejabatPenilai'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataSkpUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataSkp2022(Request $request, $id=NULL, $dt=NULL) {
    // $authenticated = $this->isAuth($request)['authenticated'];
    // $username = $this->isAuth($request)['username'];
    // if(!$authenticated) return [
    //   'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
    //   'status' => $authenticated === true ? 1 : 0
    // ];

    if ($dt !== NULL) {
      $data = $dt;
      DB::table('m_data_skp_2022')->insert([
        'id' => NULL,
        'tahun' => $data['tahun'],
        'idPerilakuKerja' => $data['perilakuKerja'],
        'idHasilKinerja' => $data['hasilKinerja'],
        'idKuadranKinerja' => $data['kuadranKinerja'],
        'nipNrpPejabatPenilai' => $data['nipNrpPejabatPenilai'],
        'namaPejabatPenilai' => $data['namaPejabatPenilai'],
        'idStatusPejabatPenilai' => $data['statusPejabatPenilai'],
        'unitOrganisasiPejabatPenilai' => $data['unitOrganisasiPejabatPenilai'],
        'jabatanPejabatPenilai' => $data['jabatanPejabatPenilai'],
        'idGolonganPejabatPenilai' => $data['golonganPejabatPenilai'],
        'idDokumen' => NULL,
        'idPegawai' => $data['idPegawai'],
        'idUsulan' => 1,
        'idUsulanStatus' => 4,
        'idUsulanHasil' => 1,
        'idDataSkp2022Update' => NULL,
        'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $data['id'],
      ]);
    }
  }

  public function updateDataSkp2022($data) { // hanya digunakan waktu sync saja
    DB::table('m_data_skp_2022')->where([
      ['idBkn', '=', $data['id']]
    ])->update([
      'tahun' => $data['tahun'],
      'idPerilakuKerja' => $data['perilakuKerja'],
      'idHasilKinerja' => $data['hasilKinerja'],
      'idKuadranKinerja' => $data['kuadranKinerja'],
      'nipNrpPejabatPenilai' => $data['nipNrpPejabatPenilai'],
      'namaPejabatPenilai' => $data['namaPejabatPenilai'],
      'idStatusPejabatPenilai' => $data['statusPejabatPenilai'],
      'unitOrganisasiPejabatPenilai' => $data['unitOrganisasiPejabatPenilai'],
      'jabatanPejabatPenilai' => $data['jabatanPejabatPenilai'],
      'idGolonganPejabatPenilai' => $data['golonganPejabatPenilai'],
      'idPegawai' => $data['idPegawai'],
      'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
  }

  public function updateDataSkp($data) { // hanya digunakan waktu sync saja
    DB::table('m_data_skp')->where([
      ['idBkn', '=', $data['idBkn']]
    ])->update([
      'idJenisJabatan'=>$data['idJenisJabatan'],
      'tahun'=>$data['tahun'],
      'idJenisPeraturanKinerja'=>$data['idJenisPeraturanKinerja'],
      'nilaiSkp'=>$data['nilaiSkp'],
      'orientasiPelayanan'=>$data['orientasiPelayanan'],
      'integritas'=>$data['integritas'],
      'komitmen'=>$data['komitmen'],
      'disiplin'=>$data['disiplin'],
      'kerjaSama'=>$data['kerjaSama'],
      'kepemimpinan'=>$data['kepemimpinan'],
      'nilaiPrestasiKerja'=>$data['nilaiPrestasiKerja'],
      'nilaiKonversi'=>$data['nilaiKonversi'],
      'nilaiIntegrasi'=>$data['nilaiIntegrasi'],
      'nilaiPerilakuKerja'=>$data['nilaiPerilakuKerja'],
      'inisiatifKerja'=>$data['inisiatifKerja'],
      'nilaiRataRata'=>$data['nilaiRataRata'],
      'jumlah'=>$data['jumlah'],
      'idStatusPejabatPenilai'=>$data['idStatusPejabatPenilai'],
      'nipNrpPejabatPenilai'=>$data['nipNrpPejabatPenilai'],
      'namaPejabatPenilai'=>$data['namaPejabatPenilai'],
      'jabatanPejabatPenilai'=>$data['jabatanPejabatPenilai'],
      'unitOrganisasiPejabatPenilai'=>$data['unitOrganisasiPejabatPenilai'],
      'golonganPejabatPenilai'=>$data['golonganPejabatPenilai'],
      'tmtGolonganPejabatPenilai'=>$data['tmtGolonganPejabatPenilai'],
      'idStatusAtasanPejabatPenilai'=>$data['idStatusAtasanPejabatPenilai'],
      'nipNrpAtasanPejabatPenilai'=>$data['nipNrpAtasanPejabatPenilai'],
      'namaAtasanPejabatPenilai'=>$data['namaAtasanPejabatPenilai'],
      'jabatanAtasanPejabatPenilai'=>$data['jabatanAtasanPejabatPenilai'],
      'unitOrganisasiAtasanPejabatPenilai'=>$data['unitOrganisasiAtasanPejabatPenilai'],
      'golonganAtasanPejabatPenilai'=>$data['golonganAtasanPejabatPenilai'],
      'tmtGolonganAtasanPejabatPenilai'=>$data['tmtGolonganAtasanPejabatPenilai'],
      'idPegawai'=>$data['idPegawai'],
      'keteranganUsulan' => 'Data sinkron dengan SIASN/MySAPK',
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
  }
}
