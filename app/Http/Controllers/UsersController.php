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
    $data = DB::table('m_pegawai')->get(['id','nip as username']);
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
    ])->get(['id','username']);
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
    $id = $message['id'];
    $user = $message['username'];
    $pwd = password_hash('12344321', PASSWORD_DEFAULT);
    $affected = 0;

    if (str_contains($user, 'admin-') || str_contains($user, 'rest-api-')) {
      $affected = DB::table('m_admin')->where([
        ['id', '=', $id]
      ])->update([
        'password' => $pwd
      ]);
    } else {
      $affected = DB::table('m_pegawai')->where([
        ['id', '=', $id]
      ])->update([
        'password' => $pwd
      ]);
    }

    $callback = [
      'message' => $affected === 1 ? 'Password berhasil direset.' : 'Password gagal direset.',
      'status' => $affected === 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  function insertUserAdmin(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $pwd = password_hash('12344321', PASSWORD_DEFAULT);
    $idAdmin = DB::table('m_admin')->insertGetId([
      'id' => NULL,
      'username' => $message['username'],
      'password' => $pwd,
      'unitOrganisasi' => $message['unitOrganisasi'],
      'idAppRoleUser' => $message['idAppRoleUser'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $callback = [
      'message' => $idAdmin > 0 ? 'User Admin berhasil ditambahkan.' : 'User Admin gagal ditambahkan.',
      'status' => $idAdmin > 0 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
