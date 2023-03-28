<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
  function getAllUserPegawai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_pegawai')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  function getAllUserAdmin(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_admin')->where([
      ['username', 'LIKE', 'admin-%']
    ])->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  function getAllUserRestApi(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_admin')->where([
      ['username', 'LIKE', 'rest-api-%']
    ])->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  function resetPassword(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $user = $message['user'];
    $pwd = $message['pwd'];
    if (str_contains($user, 'admin-') || str_contains($user, 'rest-api-')) {
      $data_ = json_decode(DB::table('m_admin')->where([
        ['username', '=', $user],
        ['password', '=', $pwd]
      ])->get(), true);
    } else {
      $data_ = json_decode(DB::table('m_pegawai')->where([
        ['username', '=', $user],
        ['password', '=', $pwd]
      ])->get(), true);
    }
    $callback = [
      'message' => 'Password gagal direset.',
      'status' => 0
    ];
    if (count($data_) == 1) {
      $pwd = password_hash('12344321', PASSWORD_DEFAULT);
      if (str_contains($user, 'admin-') || str_contains($user, 'rest-api-')) {
        $data_ = DB::table('m_admin')->where([
          ['username', '=', $user],
          ['password', '=', $pwd]
        ])->update([
          'password' => $pwd
        ]);
      } else {
        $data_ = DB::table('m_pegawai')->where([
          ['username', '=', $user],
          ['password', '=', $pwd]
        ])->update([
          'password' => $pwd
        ]);
      }
      $callback = [
        'message' => 'Password berhasil direset.',
        'status' => 1
      ];
    }
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataPegawai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $pwd = password_hash('12344321', PASSWORD_DEFAULT);
    $idPegawai = DB::table('m_pegawai')->insertGetId([
      'id' => NULL,
      'nip' => $message['nip'],
      'password' => $pwd,
      'idAppRoleUser' => 4,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    DB::table('m_data_pribadi')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'tempatLahir' => $message['tempatLahir'],
      'tanggalLahir' => $message['tanggalLahir'],
      'alamat' => $message['alamat'],
      'ktp' => $message['nik'],
      'nomorHp' => $message['nomorHp'],
      'email' => $message['email'],
      'npwp' => $message['npwp'],
      'bpjs' => $message['bpjs'],
      'idPegawai' => $idPegawai,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $callback = [
      'message' => 'Data pegawai berhasil ditambahkan',
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
