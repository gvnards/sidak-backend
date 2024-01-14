<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DokumenController extends Controller
{
  public function getDokumenKategori($keterangan, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_dokumen_kategori')->where([
      ['keterangan', 'LIKE', "%$keterangan%"]
    ])->get();
    $callback = [
      'message' => $data,
      'status' => count($data) === 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
    // return $request;
  }
  public function getDocumentCategory($keterangan) {
    $data = json_decode(DB::table('m_dokumen_kategori')->where([
      ['keterangan', 'LIKE', "%$keterangan%"]
    ])->get(), true)[0];
    return $data;
  }
  public function getDocument(Request $request, $idDokumen) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    if ($idDokumen == NULL || intval($idDokumen) === 1) {
      return '';
    }

    $listDokumenKategori = [
      'dok_elektronik_ijazah' => 'elektronik/ijazah',
      'dok_elektronik_transkrip' => 'elektronik/transkrip',
      'dok_elektronik_ibel' => 'elektronik/ibel',
      'dok_elektronik_akreditasi' => 'elektronik/akreditasi',
      'dok_akta_perkawinan' => 'pasangan',
      'dok_akta_anak' => 'anak',
      'dok_ijazah' => 'pendidikan',
      'dok_transkrip' => 'pendidikan',
      'dok_sk_pangkat' => 'pangkat',
      'dok_sertifikat_diklat' => 'diklat',
      'dok_hukuman_disiplin' => 'hukdis',
      'dok_sk_cpns' => 'cpns',
      'dok_sk_pns' => 'pns',
      'dok_skp' => 'skp',
      'dok_sk_jabatan' => 'jabatan',
      'dok_penghargaan' => 'penghargaan',
      'dok_sk_pak' => 'pak',
    ];

    $document = json_decode(DB::table('m_dokumen')->where([
      ['id', '=', $idDokumen]
    ])->get(), true);
    $folderDokumen = '';
    $namaDokumen = $document[0]['nama'];
    $namaDokumenLowerCase = strtolower($namaDokumen);

    // ** START CHECK --> cek nama dokumen
    foreach ($listDokumenKategori as $key => $value) {
      if (str_contains($namaDokumenLowerCase, $key)) {
        $folderDokumen = $value;
        break;
      }
    }
    // ** END CHECK

    $path = storage_path('app/dokumen/'.$folderDokumen.'/'.$namaDokumen.".pdf");
    try {
      $mimeType = mime_content_type($path);
      $getPdfFromServerFolder = base64_encode(file_get_contents($path));
      $blob = 'data:'.$mimeType.';base64,'.$getPdfFromServerFolder;
    } catch (Exception $ex) {
      $blob = '';
    }
    return $blob;
  }
}
