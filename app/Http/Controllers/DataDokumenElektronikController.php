<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataDokumenElektronikController extends Controller
{
  private function getDataDokumenElektronik($idPegawai) {
    $data = json_decode(DB::table('m_data_dokumen_elektronik')->join('m_daftar_dokumen_elektronik', 'm_data_dokumen_elektronik.idDaftarDokEl', '=', 'm_daftar_dokumen_elektronik.id')->join('m_dokumen', 'm_data_dokumen_elektronik.idDokumen', '=', 'm_dokumen.id')->where([
      ['m_data_dokumen_elektronik.idPegawai', '=', $idPegawai]
    ])->get([
      'm_daftar_dokumen_elektronik.id AS idDaftar',
      'm_dokumen.id AS idDokumen',
      'm_dokumen.nama AS nama',
    ]), true);
    return $data;
  }
  private function getDaftarDokumenElektronik() {
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
    $dataDokumen = $this->getDataDokumenElektronik($idPegawai);
    $daftarDokumen = $this->getDaftarDokumenElektronik();
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
    return $message;
  }
}
