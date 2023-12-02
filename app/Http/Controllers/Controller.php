<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  function getPegawaiByDate($string_date) {
    $query = "WITH jabatan_pegawai AS (
      SELECT
      *
      FROM
      v_m_jabatan
      WHERE
        idUsulanStatus IN (3, 4)
        AND idUsulanHasil = 1
        AND spmt <= '$string_date'
      GROUP BY
        idPegawai),
    pangkat_pegawai AS (
      SELECT
      *
      FROM
      v_m_pangkat
      WHERE
        idUsulanStatus IN (3, 4)
        AND idUsulanHasil = 1
        AND tmt <= '$string_date'
      GROUP BY
        idPegawai)
    SELECT
      m_pegawai.id as id,
      m_pegawai.nip as nip,
      m_data_pribadi.nama as nama,
      v_m_pendidikan_group.gelarDepan as gelarDepan,
      v_m_pendidikan_group.gelarBelakang as gelarBelakang,
      v_m_pendidikan_group.tingkatPendidikan as tingkatPendidikan,
      v_m_pendidikan_group.namaSekolah as namaSekolah,
      v_m_pendidikan_group.pendidikan as pendidikan,
      pangkat_pegawai.golongan as golongan,
      pangkat_pegawai.pangkat as pangkat,
      pangkat_pegawai.tmt as tmtGolongan,
      pangkat_pegawai.masaKerjaTahun as masaKerjaTahun,
      pangkat_pegawai.masaKerjaBulan as masaKerjaBulan,
      jabatan_pegawai.jabatan as jabatan,
      jabatan_pegawai.jenisJabatan as jenisJabatan,
      jabatan_pegawai.eselon as eselon,
      jabatan_pegawai.tmt as tmtJabatan,
      jabatan_pegawai.spmt as spmtJabatan,
      jabatan_pegawai.kodeKomponen as kodeKomponen,
      jabatan_pegawai.unitOrganisasi as unitOrganisasi
    FROM
      m_pegawai
      INNER JOIN m_data_pribadi ON m_pegawai.id = m_data_pribadi.idPegawai
      LEFT JOIN v_m_pendidikan_group ON m_pegawai.id = v_m_pendidikan_group.idPegawai
      LEFT JOIN pangkat_pegawai ON m_pegawai.id = pangkat_pegawai.idPegawai
      LEFT JOIN jabatan_pegawai ON m_pegawai.id = jabatan_pegawai.idPegawai;";
    $pegawai = DB::select($query);
    return collect($pegawai);
  }

  function isAuth(Request $request) {
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->header('Authorization'));
    $message = json_decode($message, true);
    $username = $message['username'];
    $password = $message['password'];
    $authenticated = [];
    if(!str_contains($username, 'admin')) {
      $authenticated = DB::table('m_pegawai')->where([
        ['nip', '=', $username,],
        ['password', '=', $password]
      ])->get();
    } else {
      $authenticated = DB::table('m_admin')->where([
        ['username', '=', $username],
        ['password', '=', $password]
      ])->get();
    }
    return [
      'authenticated' => count($authenticated) === 1 ? true : false,
      'username' => $username
    ];
  }

  function isAuthorized(Request $request){
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    $callback = [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $callback = $this->encrypt($username, json_encode($callback));
    return $callback;
  }

  function getBlobDokumen($idDokumen, $folderDokumen, $ekstensiDokumen) {
    if ($idDokumen == NULL) {
      $blob = '';
    } else {
      $dokumen = json_decode(DB::table('m_dokumen')->where([
        ['id', '=', $idDokumen]
      ])->get(), true)[0];
      $filename = $dokumen['nama'];
      $path = storage_path('app/dokumen/'.$folderDokumen.'/'.$filename.".".$ekstensiDokumen);
      try {
        $mimeType = mime_content_type($path);
        $getPdfFromServerFolder = base64_encode(file_get_contents($path));
        $blob = 'data:'.$mimeType.';base64,'.$getPdfFromServerFolder;
      } catch (Exception $ex) {
        $blob = '';
      }
    }
    return $blob;
  }

  function deleteDokumen($idDokumen, $folderDokumen, $ekstensiDokumen, $deleteFromDb=true) {
    if ($idDokumen == NULL) return;
    $dokumen = json_decode(DB::table('m_dokumen')->where([
      ['id', '=', $idDokumen]
    ])->get(), true)[0];
    try {
      Storage::delete('dokumen/'.$folderDokumen.'/'.$dokumen['nama'].'.'.$ekstensiDokumen);
    } catch (Exception $ex) {}
    if ($deleteFromDb) {
      DB::table('m_dokumen')->where([
        ['id', '=', $idDokumen]
      ])->delete();
    }
  }

  function uploadDokumen($namaDokumen, $blobDokumen, $ekstensiDokumen, $folderDokumen) {
    Storage::putFileAs('dokumen', $blobDokumen, $folderDokumen.'/'.$namaDokumen.'.'.$ekstensiDokumen);
  }

  /**
   * Decrypt data from a CryptoJS json encoding string
   *
   * @param mixed $passphrase
   * @param mixed $jsonString
   * @return mixed
   */
  function decrypt($passphrase, $jsonString)
  {
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
      $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
      $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
  }

  /**
   * Encrypt value to a cryptojs compatiable json encoding string
   *
   * @param mixed $passphrase
   * @param mixed $value
   * @return string
   */
  function encrypt($passphrase, $value)
  {
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
      $dx = md5($dx . $passphrase . $salt, true);
      $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
  }
}
