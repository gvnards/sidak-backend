<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataJabatanController extends Controller
{
  private function searchUnor($listUnor, $searchKey, $searchValue) {
    $countList = count($listUnor);
    for ($i = 0; $i < $listUnor; $i++) {
      if ($listUnor[$i][$searchKey] === $searchValue) return [$listUnor[$i]];
    }
    return [];
  }

  private function filterUnorThatHasIdBkn($listUnor) {
    $countList = count($listUnor);
    $unor = [];
    $unorHilang = [];
    for ($i = 0; $i < $countList; $i++) {
      if($listUnor[$i]['idBkn'] !== '' && $listUnor[$i]['idBkn'] !== null && $listUnor[$i]['kodeKomponen'] !== '431') array_push($unor, $listUnor[$i]);
      else array_push($unorHilang, $listUnor[$i]);
    }
    return [
      'unor' => $unor,
      'unorHilang' => $unorHilang
    ];
  }

  private function shortenNamaUnor($listUnor, $textPenyambung) {
    $unor = $listUnor['unor'];
    $unorHilang = $listUnor['unorHilang'];
    /// persingkat nama unor dengan cara menghapus nama sesuai dengan unor yang dilihangkan (tdk punya idBkn)
    $countUnor = count($unor);
    $countUnorHilang = count($unorHilang);
    for ($i = 0; $i < $countUnor; $i++) {
      for ($j = 0; $j < $countUnorHilang; $j++) {
        if (str_contains($unor[$i]['kodeKomponen'], $unorHilang[$j]['kodeKomponen'])) {
          $explodeNamaUnor = explode($textPenyambung, $unor[$i]['nama']);
          $countExplodeNamaUnor = count($explodeNamaUnor);
          $namaUnorHilang = explode($textPenyambung, $unorHilang[$j]['nama'])[0];
          $explodeNamaUnorResult = [];
          for ($l = 0; $l < $countExplodeNamaUnor; $l++) {
            if ($explodeNamaUnor[$l] !== $namaUnorHilang) array_push($explodeNamaUnorResult, $explodeNamaUnor[$l]);
          }
          $unor[$i]['nama'] = implode($textPenyambung, $explodeNamaUnorResult);
        }
      }
    }
    return $unor;
  }

  public function getAllUnor() {
    $unor = json_decode(DB::table('m_unit_organisasi')->where('kodeKomponen', 'NOT LIKE', '-%')->get(), true);
    $textPenyambung = " pada ";
    $countUnor = count($unor);
    for ($i = 0; $i < $countUnor; $i++) {
      $explodeKodeKomponen = explode(".", $unor[$i]['kodeKomponen']);
      if (count($explodeKodeKomponen) > 1) {
        array_pop($explodeKodeKomponen);
        $searchKodeKomponen = implode('.', $explodeKodeKomponen);
        $searchUnor = $this->searchUnor($unor, 'kodeKomponen', $searchKodeKomponen);
        if (count($searchUnor) > 0) {
          $unor[$i]['nama'] = $unor[$i]['nama'].$textPenyambung.$searchUnor[0]['nama'];
        }
      }
    }
    $unorFiltered = $this->filterUnorThatHasIdBkn($unor);
    $shortenNamaUnor = $this->shortenNamaUnor($unorFiltered, $textPenyambung);
    return $shortenNamaUnor;
  }
  public function getDataJabatan(Request $request, $idPegawai, $idDataJabatan=null) {
    if($idDataJabatan === null) {
      $authenticated = $this->isAuth($request)['authenticated'];
      $username = $this->isAuth($request)['username'];
      if(!$authenticated) return [
        'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
        'status' => $authenticated === true ? 1 : 0
      ];
      $data = DB::table('m_pegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_jabatan.idUsulanHasil', '=', 1],
        ['m_data_jabatan.idUsulan', '=', 1],
      ])->orderBy('m_data_jabatan.tmt', 'desc')->get([
        'm_data_jabatan.id as id',
        'm_jabatan.nama as jabatan',
        'm_jabatan.kodeKomponen as kodeKomponen',
        'm_jenis_jabatan.nama as jenisJabatan'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_kelas_jabatan', 'm_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_unit_organisasi', 'm_jabatan.kodeKomponen', '=', 'm_unit_organisasi.kodeKomponen')->leftJoin('m_eselon', 'm_jabatan.idEselon', '=', 'm_eselon.id')->where([
        ['m_data_jabatan.id', '=', $idDataJabatan],
      ])->get([
        'm_data_jabatan.*',
        'm_jabatan.nama as jabatan',
        'm_jabatan.kodeKomponen as kodeKomponen',
        'm_unit_organisasi.nama as unitOrganisasi'
      ]), true);
      // $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'jabatan', 'pdf');
      $data[0]['dokumen'] = '';
      return $data;
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function insertDataJabatan($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);

    /// ** START CHECK --> cek apakah data yang akan diusulkan itu sudah pernah diusulkan sebelumnya atau belum
    if ($id === NULL) {
      $checkData = json_decode(DB::table('m_data_jabatan')->where([
        ['m_data_jabatan.idPegawai', '=', intval($message['idPegawai'])],
        ['m_data_jabatan.idUsulan', '=', 1],
        ['m_data_jabatan.idUsulanHasil', '=', 3]
      ])->get(), true);
      if (count($checkData) > 0) return [
        'message' => "Maaf, Data Jabatan sudah ada yang ditambahkan tetapi belum diverifikasi.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
        'status' => 3
      ];
    } else {
      $checkData = json_decode(DB::table('m_data_jabatan')->where([
        ['idDataJabatanUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get(), true);
      if (count($checkData) > 0) return [
        'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
        'status' => 3
      ];
    }
    /// ** END CHECK

    /// ** START CHECK --> cek apakah kodeKomponen (unor) sama dengan kodeKomponenJabatan, kalo tidak, maka tambahkan Jabatan Baru
    if ($message['kodeKomponen'] !== $message['kodeKomponenJabatan']) {
      $jabatanBaru = json_decode(DB::table('m_jabatan')->where([
        ['id', '=', $message['idJabatan']]
      ])->get()->toJson(), true)[0];
      $idJabatanBaru = DB::table('m_jabatan')->insertGetId([
        'id' => NULL,
        'nama' => $jabatanBaru['nama'],
        'kebutuhan' => -1,
        'idKelasJabatan' => $jabatanBaru['idKelasJabatan'],
        'target' => $jabatanBaru['target'],
        'kodeKomponen' => $message['kodeKomponen'],
        'idJenisJabatan' => $jabatanBaru['idJenisJabatan'],
        'idEselon' => $jabatanBaru['idEselon'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $jabatanBaru['idBkn']
      ]);
      $message['idJabatan'] = $idJabatanBaru;
    }
    /// ** END CHECK

    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $ley => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_SK_JABATAN_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SK_JABATAN_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'jabatan');

    $data = DB::table('m_data_jabatan')->insert([
      'id' => NULL,
      'idJabatan' => $message['idJabatan'],
      'isPltPlh' => $message['isPltPlh'],
      'idJabatanTugasTambahan' => $message['idJabatanTugasTambahan'],
      'tmt' => $message['tmt'],
      'spmt' => $message['spmt'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataJabatanUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }

  private function getAllJabatan() {
    // $data = json_decode(DB::table('m_jabatan')->get(), true);
    $data = json_decode(DB::table('v_m_daftar_jabatan')->join('m_kelas_jabatan', 'v_m_daftar_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->get([
      'v_m_daftar_jabatan.id as id',
      'v_m_daftar_jabatan.nama as nama',
      'v_m_daftar_jabatan.kebutuhan as kebutuhan',
      'v_m_daftar_jabatan.kodeKomponen as kodeKomponen',
      'v_m_daftar_jabatan.terisi as jabatanTerisi',
    ])->toJson(), true);
    $jabatanGroup = json_decode(DB::table('v_m_daftar_jabatan')->join('m_kelas_jabatan', 'v_m_daftar_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->where([
      ['v_m_daftar_jabatan.terisi', '>', 0]
    ])->orWhere([
      ['v_m_daftar_jabatan.kebutuhan', '>', -1]
    ])->groupBy('v_m_daftar_jabatan.nama')->get([
      'v_m_daftar_jabatan.id as id',
      'v_m_daftar_jabatan.nama as nama',
      'v_m_daftar_jabatan.kodeKomponen as kodeKomponen',
    ])->toJson(), true);
    for($i=0; $i<count($jabatanGroup); $i++) {
      $jabatanGroup[$i]['kebutuhan'] = -1;
      $jabatanGroup[$i]['jabatanTerisi'] = 0;
      $jabatanGroup[$i]['nama'] = $jabatanGroup[$i]['nama']." (Tidak ada di dalam Peta Jabatan Unit Organisasi)";
      array_push($data, $jabatanGroup[$i]);
    }
    return [
      'jabatan' => $data,
      'jabatanGroup' => $jabatanGroup
    ];
  }

  private function getAllUnitOrganisasi() {
    $data = json_decode(DB::table('m_unit_organisasi')->get(), true);
    return $data;
  }

  private function getAllTugasTambahan() {
    $data = json_decode(DB::table('m_jabatan_tugas_tambahan')->get(), true);
    return $data;
  }

  public function getDataJabatanCreated(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jabatan = $this->getAllJabatan();
    $unitOrganisasi = $this->getAllUnor();
    $tugasTambahan = $this->getAllTugasTambahan();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('jabatan');
    $callback = [
      'message' => [
        'jabatan' => $jabatan,
        'unitOrganisasi' => $unitOrganisasi,
        'tugasTambahan' => $tugasTambahan,
        'dokumenKategori' => $dokumenKategori,
      ],
      'status' => 2
    ];
    return $callback;
  }

  public function getDataJabatanDetail(Request $request, $idPegawai, $idDataJabatan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $jabatan = $this->getAllJabatan();
    $unitOrganisasi = $this->getAllUnor();
    $tugasTambahan = $this->getAllTugasTambahan();
    $dataJabatanUnitOrganisasi = $this->getDataJabatan($request, $idPegawai, $idDataJabatan);
    $dokumenKategori = (new DokumenController)->getDocumentCategory('jabatan');
    $callback = [
      'message' => [
        'jabatan' => $jabatan,
        'unitOrganisasi' => $unitOrganisasi,
        'tugasTambahan' => $tugasTambahan,
        'dokumenKategori' => $dokumenKategori,
        'dataJabatanUnitOrganisasi' => $dataJabatanUnitOrganisasi
      ],
      'status' => 2
    ];
    return $callback;
  }
}
