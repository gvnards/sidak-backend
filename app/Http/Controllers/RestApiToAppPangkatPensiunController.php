<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestApiToAppPangkatPensiunController extends RestApiController
{
  private function restDataJabatans($asnId, $periode) {
    $data = json_decode(DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_unit_organisasi AS unor_child', 'm_jabatan.kodeKomponen', '=', 'unor_child.kodeKomponen')->leftJoin('m_unit_organisasi AS unor_parent', 'unor_child.idBknAtasan', '=', 'unor_parent.idBkn')->leftJoin('m_dokumen', 'm_data_jabatan.idDokumen', '=', 'm_dokumen.id')->whereIn('m_data_jabatan.idUsulanStatus', [3,4])->where([
      ['m_data_jabatan.idPegawai', '=', $asnId],
      ['m_data_jabatan.tmt', '<=', $periode],
      ['m_data_jabatan.idUsulan', '=', 1],
      ['m_data_jabatan.idUsulanHasil', '=', 1]
    ])->orderBy('m_data_jabatan.tmt', 'desc')->get([
      'm_jenis_jabatan.nama AS jabatan_jenis',
      'm_jabatan.nama AS jabatan_nama',
      'm_data_jabatan.tmt AS jabatan_tmt',
      'unor_child.nama AS jabatan_unor',
      'unor_parent.nama AS jabatan_unor_induk',
      'm_data_jabatan.tanggalDokumen AS jabatan_dokumen_tanggal',
      'm_data_jabatan.nomorDokumen AS jabatan_dokumen_nomor',
      'm_dokumen.nama AS jabatan_dokumen_url'
    ]), true);
    for($i = 0; $i < count($data); $i++) {
      if($data[$i]['jabatan_dokumen_url'] !== null) {
        $data[$i]['jabatan_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['jabatan_dokumen_url'];
      }
    }
    return $data;
  }
  private function restDataAngkaKredits($asnId) {
    $data = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->leftJoin('m_dokumen', 'm_data_angka_kredit.idDokumen', '=', 'm_dokumen.id')->whereIn('m_data_angka_kredit.idUsulanStatus', [3,4])->where([
      ['m_data_angka_kredit.idPegawai', '=', $asnId],
      ['m_data_angka_kredit.idUsulan', '=', 1],
      ['m_data_angka_kredit.idUsulanHasil', '=', 1]
    ])->get([
      'm_daftar_jenis_angka_kredit.jenisAngkaKredit AS kredit_jenis',
      'm_jabatan.nama AS kredit_jabatan',
      'm_data_angka_kredit.tahun AS kredit_tahun',
      'm_data_angka_kredit.periodePenilaianMulai AS kredit_periode_mulai',
      'm_data_angka_kredit.periodePenilaianSelesai AS kredit_periode_selesai',
      'm_data_angka_kredit.angkaKreditUtama AS kredit_nilai_ak_utama',
      'm_data_angka_kredit.angkaKreditPenunjang AS kredit_nilai_ak_penunjang',
      'm_data_angka_kredit.angkaKreditTotal AS kredit_nilai_ak_total',
      'm_data_angka_kredit.tanggalDokumen AS kredit_dokumen_tanggal',
      'm_data_angka_kredit.nomorDokumen AS kredit_dokumen_nomor',
      'm_dokumen.nama AS kredit_dokumen_url'
    ]), true);
    for($i = 0; $i < count($data); $i++) {
      if($data[$i]['kredit_dokumen_url'] !== null) {
        $data[$i]['kredit_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['kredit_dokumen_url'];
      }
    }
    return $data;
  }
  private function restDataHukdiss($asnId) {
    $data = json_decode(DB::table('m_data_hukuman_disiplin')->join('m_jenis_hukuman_disiplin', 'm_data_hukuman_disiplin.idJenisHukumanDisiplin', '=', 'm_jenis_hukuman_disiplin.id')->join('m_daftar_hukuman_disiplin', 'm_data_hukuman_disiplin.idDaftarHukumanDisiplin', '=', 'm_daftar_hukuman_disiplin.id')->join('m_daftar_dasar_hukum_hukuman_disiplin', 'm_data_hukuman_disiplin.idDaftarDasarHukumHukdis', '=', 'm_daftar_dasar_hukum_hukuman_disiplin.id')->join('m_daftar_alasan_hukuman_disiplin', 'm_data_hukuman_disiplin.idDaftarAlasanHukdis', '=', 'm_daftar_alasan_hukuman_disiplin.id')->leftJoin('m_dokumen', 'm_data_hukuman_disiplin.idDokumen', '=', 'm_dokumen.id')->whereIn('m_data_hukuman_disiplin.idUsulanStatus', [3,4])->where([
      ['m_data_hukuman_disiplin.idPegawai', '=', $asnId],
      ['m_data_hukuman_disiplin.idUsulan', '=', 1],
      ['m_data_hukuman_disiplin.idUsulanHasil', '=', 1]
    ])->get([
      'm_jenis_hukuman_disiplin.nama AS hukdis_jenis',
      'm_daftar_hukuman_disiplin.nama AS hukdis_hukuman',
      'm_daftar_dasar_hukum_hukuman_disiplin.nama AS hukdis_dasar_hukum',
      'm_daftar_alasan_hukuman_disiplin.nama AS hukdis_alasan',
      'm_data_hukuman_disiplin.tmtAwal AS hukdis_tmt_awal',
      'm_data_hukuman_disiplin.tmtAkhir AS hukdis_tmt_akhir',
      'm_data_hukuman_disiplin.masaHukuman AS hukdis_masa_hukuman_bulan',
      'm_data_hukuman_disiplin.keteranganAlasanHukdis AS hukdis_keterangan_alasan',
      'm_data_hukuman_disiplin.nomorDokumen AS hukdis_dokumen_nomor',
      'm_data_hukuman_disiplin.tanggalDokumen AS hukdis_dokumen_tanggal',
      'm_dokumen.nama AS hukdis_dokumen_url'
    ]), true);
    for($i = 0; $i < count($data); $i++) {
      if($data[$i]['hukdis_dokumen_url'] !== null) {
        $data[$i]['hukdis_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['hukdis_dokumen_url'];
      }
    }
    return $data;
  }
  private function restDataSkps($asnId) {
    $dataSkp = json_decode(DB::table('m_data_skp')->join('m_jenis_jabatan', 'm_data_skp.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_jenis_peraturan_kinerja', 'm_data_skp.idJenisPeraturanKinerja', '=', 'm_jenis_peraturan_kinerja.id')->leftJoin('m_dokumen', 'm_data_skp.idDokumen', '=', 'm_dokumen.id')->orderBy('m_data_skp.tahun', 'desc')->whereIn('m_data_skp.idUsulanStatus', [3,4])->where([
      ['m_data_skp.idPegawai', '=', $asnId],
      ['m_data_skp.idUsulan', '=', 1],
      ['m_data_skp.idUsulanHasil', '=', 1]
    ])->get([
      'm_jenis_jabatan.nama as jenisJabatan',
      'm_data_skp.tahun as tahun',
      'm_jenis_peraturan_kinerja.nama as jenisPeraturanKinerja',
      'm_data_skp.nilaiSkp as nilaiSkp',
      'm_data_skp.orientasiPelayanan as nilaiOrientasiPelayanan',
      'm_data_skp.integritas as nilaiIntegritas',
      'm_data_skp.komitmen as nilaiKomitmen',
      'm_data_skp.disiplin as nilaiDisiplin',
      'm_data_skp.kerjaSama as nilaiKerjaSama',
      'm_data_skp.kepemimpinan as nilaiKepemimpinan',
      'm_data_skp.nilaiPrestasiKerja as nilaiPrestasiKerja',
      'm_data_skp.nilaiKonversi as nilaiKonversi',
      'm_data_skp.nilaiIntegrasi as nilaiIntegrasi',
      'm_data_skp.nilaiPerilakuKerja as nilaiPerilakuKerja',
      'm_data_skp.inisiatifKerja as nilaiInisiatifKerja',
      'm_data_skp.nilaiRataRata as nilaiRataRata',
      'm_data_skp.jumlah as nilaiJumlah',
      'm_dokumen.nama AS skp_dokumen_url'
    ]), true);
    $dataSkp2022 = json_decode(DB::table('m_data_skp_2022')->join('m_daftar_nilai_kerja_kinerja as perilakuKerja', 'm_data_skp_2022.idPerilakuKerja', '=', 'perilakuKerja.id')->join('m_daftar_nilai_kerja_kinerja as hasilKinerja', 'm_data_skp_2022.idHasilKinerja', '=', 'hasilKinerja.id')->join('m_daftar_nilai_kuadran', 'm_data_skp_2022.idKuadranKinerja', '=', 'm_daftar_nilai_kuadran.id')->leftJoin('m_dokumen', 'm_data_skp_2022.idDokumen', '=', 'm_dokumen.id')->orderBy('m_data_skp_2022.tahun', 'desc')->whereIn('m_data_skp_2022.idUsulanStatus', [3,4])->where([
      ['m_data_skp_2022.idPegawai', '=', $asnId],
      ['m_data_skp_2022.idUsulan', '=', 1],
      ['m_data_skp_2022.idUsulanHasil', '=', 1]
    ])->get([
      'm_data_skp_2022.tahun as tahun',
      'perilakuKerja.nama as perilakuKerja',
      'hasilKinerja.nama as hasilKinerja',
      'm_daftar_nilai_kuadran.nama as kuadranKinerja',
      'm_dokumen.nama AS skp_dokumen_url'
    ]), true);
    $data = array_merge($dataSkp2022, $dataSkp);
    for($i = 0; $i < count($data); $i++) {
      if($data[$i]['skp_dokumen_url'] !== null) {
        $data[$i]['skp_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$data[$i]['skp_dokumen_url'];
      }
    }
    return $data;
  }
  public function restGet(Request $request, $nipBaru, $periode) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $dataSingle = json_decode(DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->join('m_daftar_pangkat', 'm_data_pangkat.idDaftarPangkat', '=', 'm_daftar_pangkat.id')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->join('m_tingkat_pendidikan', 'm_data_pendidikan.idTingkatPendidikan', '=', 'm_tingkat_pendidikan.id')->join('m_daftar_pendidikan', 'm_data_pendidikan.idDaftarPendidikan', '=', 'm_daftar_pendidikan.id')->whereIn('m_data_pangkat.idUsulanStatus', [3,4])->whereIn('m_data_pendidikan.idUsulanStatus', [3,4])->where([
      ['m_pegawai.nip', '=', $nipBaru],
      ['m_data_pangkat.tmt', '<=', $periode],
      ['m_data_pendidikan.tanggalDokumen', '<=', $periode],
      ['m_data_pangkat.idUsulan', '=', 1],
      ['m_data_pangkat.idUsulanHasil', '=', 1],
      ['m_data_pendidikan.idUsulan', '=', 1],
      ['m_data_pendidikan.idUsulanHasil', '=', 1]
    ])->orderBy('m_daftar_pangkat.id', 'desc')->orderBy('m_tingkat_pendidikan.id', 'desc')->limit(1)->get([
      'm_pegawai.id AS asn_id',
      'm_pegawai.nip AS asn_nip',
      'm_data_pribadi.nama AS nama',
      'm_data_pribadi.tempatLahir AS asn_tempat_lahir',
      'm_data_pribadi.tanggalLahir AS asn_tanggal_lahir',
      'm_daftar_pangkat.golongan AS asn_golongan',
      'm_daftar_pangkat.pangkat AS asn_pangkat',
      'm_data_pangkat.tmt AS asn_tmt_golongan',
      'm_data_pangkat.masaKerjaTahun AS asn_mk_golongan_tahun',
      'm_data_pangkat.masaKerjaBulan AS asn_mk_golongan_bulan',
      'm_tingkat_pendidikan.nama AS asn_tingkat_pendidikan',
      'm_daftar_pendidikan.nama AS asn_pendidikan'
    ]), true);
    foreach ($dataSingle as $idx => $dt) {
      $dataSingle[$idx]['asn_jabatans'] = $this->restDataJabatans($dt['asn_id'], $periode);
      $dataSingle[$idx]['asn_kredits'] = $this->restDataAngkaKredits($dt['asn_id']);
      $dataSingle[$idx]['asn_hukdiss'] = $this->restDataHukdiss($dt['asn_id']);
      $dataSingle[$idx]['asn_skps'] = $this->restDataSkps($dt['asn_id']);
    }

    return $dataSingle;

    ///// IKI BERLAKU SEARCH 1 ORANG
    // SELECT
    //   m_pegawai.nip,
    //   m_data_pribadi.nama,
    //   m_data_pribadi.tempatLahir,
    //   m_data_pribadi.tanggalLahir,
    //   m_daftar_pangkat.golongan,
    //   m_daftar_pangkat.pangkat,
    //   m_tingkat_pendidikan.nama AS 'tingkat_pendidikan',
    //   m_daftar_pendidikan.nama AS 'pendidikan'
    // FROM
    //   m_pegawai
    //   INNER JOIN m_data_pribadi ON m_pegawai.id = m_data_pribadi.idPegawai
    //   INNER JOIN m_data_pangkat ON m_pegawai.id = m_data_pangkat.idPegawai
    //   INNER JOIN m_daftar_pangkat ON m_data_pangkat.idDaftarPangkat = m_daftar_pangkat.id
    //   INNER JOIN m_data_pendidikan ON m_pegawai.id = m_data_pendidikan.idPegawai
    //   INNER JOIN m_tingkat_pendidikan ON m_data_pendidikan.idTingkatPendidikan = m_tingkat_pendidikan.id
    //   INNER JOIN m_daftar_pendidikan ON m_data_pendidikan.idDaftarPendidikan = m_daftar_pendidikan.id
    // WHERE
    //   m_pegawai.nip = '196910142008011011'
    // ORDER BY
    //   m_daftar_pangkat.id DESC,
    //   m_tingkat_pendidikan.id DESC
    // LIMIT 1;
  }
}
