<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RestApiController extends Controller
{
  public function getRestTokenAdmin(Request $request, $username) {
    $usr = $username;
    $user = json_decode(DB::table('m_admin')->where([
      ['username', '=', $usr]
    ])->get(), true)[0];
    $callback = [
      'id' => $user['id'],
      'username' => $user['username'],
      'password' => $user['password']
    ];
    $token = $this->encrypt('sidak.bkpsdmsitubondokab', $callback);
    $token = str_replace("{", "", $token);
    $token = str_replace("}", "", $token);
    $token = str_replace('"', "!|xm|>", $token);
    return $token;
  }

  private function getRestTokenPegawai($objectPegawai) {
    $callback = [
      'id' => $objectPegawai['id'],
      'username' => $objectPegawai['nip'],
      'password' => $objectPegawai['password']
    ];
    $token = $this->encrypt('sidak.bkpsdmsitubondokab', $callback);
    $token = str_replace("{", "", $token);
    $token = str_replace("}", "", $token);
    $token = str_replace('"', "!|xm|>", $token);
    return $token;
  }

  private function reformToken($token) {
    $token = $token;
    $token = "{".$token."}";
    $token = str_replace("!|xm|>", '"', $token);
    return $token;
  }

  public function createRestUser(Request $request) {
    $pswd = password_hash($this->generatePassword(), PASSWORD_DEFAULT);
    DB::table('m_admin')->insert([
      'id' => NULL,
      'username' => $request->username,
      'password' => $pswd,
      'unitOrganisasi' => 431,
      'idAppRoleUser' => 1,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $callback = [
      'status' => 2,
      'message' => 'Rest User berhasil dibuat.'
    ];
    return $callback;
  }

  public function generatePassword() {
    $letters = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z']; // 0
    $numbers = [0,1,2,3,4,5,6,7,8,9]; // 1
    $specials = ['!','@','#','$','%','^','&','*','-','_','+','=']; // 2
    $pswd = '';
    do {
      $random = rand(0,2);
      if ($random === 0) {
        $pswd = $pswd.$letters[rand(0,count($letters)-1)];
      } else if ($random === 1) {
        $pswd = $pswd.$numbers[rand(0,count($numbers)-1)];
      } else {
        $pswd = $pswd.$specials[rand(0,count($specials)-1)];
      }
    } while (strlen($pswd) < 8);
    return $pswd;
  }

  protected function isRestAuth($token) {
    if ($token === null) return [
      'status' => false,
      'message' => 'Forbidden'
    ];
    $tkn = $this->reformToken($token);
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $tkn);
    if ($message === null) return [
      'status' => false,
      'message' => 'Forbidden'
    ];
    $message = json_decode(DB::table('m_admin')->where([
      ['username', '=', $message['username']],
      ['password', '=', $message['password']]
    ])->get(), true);
    return [
      'status' => count($message) > 0,
      'message' => count($message) > 0 ? 'Authenticated' : 'Forbidden'
    ];
  }

  public function restLogin(Request $request) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $usr = $request->username;
    $pwd = $request->password;
    $users = json_decode(DB::table('m_pegawai')->where([
      ['nip', '=', $usr],
    ])->get(), true);
    if (count($users) === 0) {
      return [
        'status' => false,
        'message' => 'Username/Password salah!'
      ];
    }

    $userFind = [];
    foreach ($users as $idx => $user) {
      if ($user['nip'] === $usr && password_verify($pwd, $user['password'])) {
        array_push($userFind, $user);
      }
    }
    if (count($userFind) === 1) {
      return [
        'status' => true,
        'message' => $this->getRestTokenPegawai($userFind[0])
      ];
    }

    /// Jika proses pengecekan password salah tapi nip benar
    $response = (new ApiSiasnController)->getAuthToken($usr, $pwd);
    if(!isset($response['access_token'])) {
      return [
        'status' => false,
        'message' => 'Username/password salah!',
      ];
    }
    $newPwd = password_hash($pwd, PASSWORD_DEFAULT);
    DB::table('m_pegawai')->where([
      ['nip', '=', $usr]
    ])->update([
      'password' => $newPwd
    ]);

    $userFind = [];
    foreach ($users as $idx => $user) {
      if ($user['nip'] === $usr && password_verify($pwd, $user['password'])) {
        array_push($userFind, $user);
      }
    }
    if (count($userFind) === 1) {
      return [
        'status' => true,
        'message' => $this->getRestTokenPegawai($userFind[0])
      ];
    }
    return [
      'status' => false,
      'message' => 'Error! Silahkan menghubungi Admin BKPSDM!'
    ];
  }

  public function restGetDocument(Request $request, $namaDokumen=NULL) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $dokumen = '';
    if ($namaDokumen !== NULL && str_contains($namaDokumen, 'DOK_')) {
      $folderDokumen = '';
      if (str_contains($namaDokumen, '_ELEKTRONIK_')) {
        if (str_contains($namaDokumen, '_IJAZAH_')) $folderDokumen = 'pendidikan';
        else if (str_contains($namaDokumen, '_IBEL_')) $folderDokumen = 'ibel';
        else if (str_contains($namaDokumen, '_AKREDITASI_')) $folderDokumen = 'akreditasi';
      }
      else if (str_contains($namaDokumen, '_JABATAN_')) $folderDokumen = 'jabatan';
      else if (str_contains($namaDokumen, '_PAK_')) $folderDokumen = 'pak';
      else if (str_contains($namaDokumen, '_IJAZAH_')) $folderDokumen = 'pendidikan';
      else if (str_contains($namaDokumen, '_TRANSKRIP_')) $folderDokumen = 'pendidikan';
      else if (str_contains($namaDokumen, '_SKP_')) $folderDokumen = 'skp';
      else if (str_contains($namaDokumen, '_HUKUMAN_DISIPLIN_')) $folderDokumen = 'hukdis';
      else if (str_contains($namaDokumen, '_PANGKAT_')) $folderDokumen = 'pangkat';
      else if (str_contains($namaDokumen, '_CPNS_')) $folderDokumen = 'cpns';
      else if (str_contains($namaDokumen, '_PNS_')) $folderDokumen = 'pns';
      else if (str_contains($namaDokumen, '_PERKAWINAN_')) $folderDokumen = 'pasangan';
      else if (str_contains($namaDokumen, '_ANAK_')) $folderDokumen = 'anak';
      else if (str_contains($namaDokumen, '_DIKLAT_')) $folderDokumen = 'diklat';
      else if (str_contains($namaDokumen, '_HUKUMAN_DISIPLIN_')) $folderDokumen = 'hukdis';
      else if (str_contains($namaDokumen, '_PENGHARGAAN_')) $folderDokumen = 'penghargaan';
      $dokumen = file_get_contents(storage_path('app/dokumen/'.$folderDokumen.'/'.$namaDokumen.".pdf"));
    }
    return response($dokumen, 200, [
      'Content-Type' => 'application/pdf'
    ]);
  }
}
