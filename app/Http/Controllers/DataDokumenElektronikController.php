<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataDokumenElektronikController extends Controller
{
  private function dataDokumenElektronik($idPegawai) {
    $data = json_decode(DB::table('m_data_dokumen_elektronik')->join('m_daftar_dokumen_elektronik', 'm_data_dokumen_elektronik.idDaftarDokEl', '=', 'm_daftar_dokumen_elektronik.id')->join('m_dokumen', 'm_data_dokumen_elektronik.idDokumen', '=', 'm_dokumen.id')->where([
      ['m_data_dokumen_elektronik.idPegawai', '=', $idPegawai]
    ])->get([
      'm_daftar_dokumen_elektronik.id AS idDaftar',
      'm_dokumen.id AS idDokumen',
      'm_dokumen.nama AS nama',
    ]), true);
    return $data;
  }
  private function dataDokumenElektronikDetail($idPegawai, $idDaftarDokumen) {
    $data = json_decode(DB::table('m_data_dokumen_elektronik')->join('m_daftar_dokumen_elektronik', 'm_data_dokumen_elektronik.idDaftarDokEl', '=', 'm_daftar_dokumen_elektronik.id')->join('m_dokumen', 'm_data_dokumen_elektronik.idDokumen', '=', 'm_dokumen.id')->where([
      ['m_data_dokumen_elektronik.idPegawai', '=', $idPegawai],
      ['m_data_dokumen_elektronik.idDaftarDokEl', '=', $idDaftarDokumen]
    ])->get([
      'm_data_dokumen_elektronik.nomorDokumen AS nomorDokumen',
      'm_data_dokumen_elektronik.tanggalDokumen AS tanggalDokumen',
      'm_daftar_dokumen_elektronik.kategori AS kategori',
      'm_dokumen.id AS idDokumen',
    ]), true)[0];
    $blobDokumen = $this->getBlobDokumen($data['idDokumen'], 'elektronik/'.$data['kategori'], 'pdf');
    return [
      'nomorDokumen' => $data['nomorDokumen'],
      'tanggalDokumen' => $data['tanggalDokumen'],
      'dokumen' => $blobDokumen
    ];
  }
  private function daftarDokumenElektronik() {
    $data = json_decode(DB::table('m_daftar_dokumen_elektronik')->get([
      'id','nama','kategori','keterangan'
    ]), true);
    return $data;
  }
  public function getDataCreated(Request $request, $idPegawai) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $dataDokumen = $this->dataDokumenElektronik($idPegawai);
    $daftarDokumen = $this->daftarDokumenElektronik();
    $dokumenKategori = (new DokumenController)->getDocumentCategory('elektronik');
    $callback = [
      'dataDokumen' => $dataDokumen,
      'daftarDokumen' => $daftarDokumen,
      'dokumenKategori' => $dokumenKategori
    ];

    return $this->encrypt($username, json_encode($callback));
  }
  public function insertDataDokumenElektronik(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $pegawai = json_decode(DB::table('m_pegawai')->where([
      ['m_pegawai.id', '=', $message['idPegawai']]
    ])->get(), true)[0];
    $nip = $pegawai['nip'];
    $dokumenElektronik = json_decode(DB::table('m_daftar_dokumen_elektronik')->where([
      ['m_daftar_dokumen_elektronik.id', '=', $message['idDaftarDokumen']]
    ])->get(), true)[0];
    $idDokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_ELEKTRONIK_".strtoupper($dokumenElektronik['kategori']).'_'.$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_ELEKTRONIK_".strtoupper($dokumenElektronik['kategori']).'_'.$nip."_".$message['date'],$message['dokumen'], 'pdf', "elektronik/".$dokumenElektronik['kategori']);
    $affected = DB::table('m_data_dokumen_elektronik')->insert([
      'idPegawai' => $message['idPegawai'],
      'idDaftarDokEl' => $message['idDaftarDokumen'],
      'idDokumen' => $idDokumen,
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $callback = [
      'message' => $affected == 1 ? "Data berhasil disimpan." : "Data gagal disimpan.",
      'status' => $affected == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function updateDataDokumenElektronik(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);

    $dataDokumen = json_decode(DB::table('m_data_dokumen_elektronik')->join('m_dokumen', 'm_data_dokumen_elektronik.idDokumen', '=', 'm_dokumen.id')->join('m_daftar_dokumen_elektronik', 'm_data_dokumen_elektronik.idDaftarDokEl', '=', 'm_daftar_dokumen_elektronik.id')->where([
      ['m_data_dokumen_elektronik.idPegawai', '=', intval($message['idPegawai'])],
      ['m_data_dokumen_elektronik.idDaftarDokEl', '=', intval($message['idDaftarDokumen'])]
    ])->get([
      'm_dokumen.*',
      'm_daftar_dokumen_elektronik.kategori'
    ]), true)[0];

    // $dokumen = json_decode(DB::table())
    $this->deleteDokumen(intval($dataDokumen['id']), "elektronik/".$dataDokumen['kategori'], 'pdf', false);
    $this->uploadDokumen($dataDokumen['nama'],$message['dokumen'], 'pdf', "elektronik/".$dataDokumen['kategori']);

    $affected = DB::table('m_data_dokumen_elektronik')->where([
      ['m_data_dokumen_elektronik.idPegawai', '=', $message['idPegawai']],
      ['m_data_dokumen_elektronik.idDaftarDokEl', '=', $message['idDaftarDokumen']]
    ])->update([
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);

    $callback = [
      'message' => $affected == 1 ? "Data berhasil diubah." : "Data gagal diubah.",
      'status' => $affected == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
  public function getDataDokumenElektronikDetail(Request $request, $idPegawai, $idDaftarDokumen) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = $this->dataDokumenElektronikDetail($idPegawai, $idDaftarDokumen);
    $callback = [
      'status' => 2,
      'message' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
