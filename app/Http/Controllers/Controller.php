<?php

namespace App\Http\Controllers;

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
      $mimeType = mime_content_type($path);
      $getPdfFromServerFolder = base64_encode(file_get_contents($path));
      $blob = 'data:'.$mimeType.';base64,'.$getPdfFromServerFolder;
    }
    return $blob;
  }

  function deleteDokumen($idDokumen, $folderDokumen, $ekstensiDokumen) {
    $dokumen = json_decode(DB::table('m_dokumen')->where([
      ['id', '=', $idDokumen]
    ])->get(), true)[0];
    Storage::delete('dokumen/'.$folderDokumen.'/'.$dokumen['nama'].'.'.$ekstensiDokumen);
    DB::table('m_dokumen')->delete($idDokumen);
    DB::table('m_dokumen')->where([
      ['id', '=', $idDokumen]
    ])->delete();
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
