<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
  public function login(Request $request) {
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->message);
    $message = json_decode($message, true);
    $username = $message['username'];
    $password = $message['password'];
    $users = [];
    if(!str_contains($username, 'admin')) {
      $users = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_app_role_user', 'm_pegawai.idAppRoleUser', '=', 'm_app_role_user.id')->where([
        ['m_pegawai.nip', '=', $username]
      ])->get([
        'm_pegawai.id as id',
        'm_pegawai.nip as nip',
        'm_pegawai.password as password',
        'm_data_pribadi.nama as nama',
        'm_app_role_user.id as idAppRoleUser',
        'm_app_role_user.nama as appRoleUser'
      ]);
    } else {
      $users = DB::table('m_admin')->join('m_app_role_user', 'm_admin.idAppRoleUser', '=', 'm_app_role_user.id')->where([
        ['username', '=', $username]
      ])->get([
        'm_admin.id as id',
        'm_admin.username as username',
        'm_admin.password as password',
        'm_admin.username as nama',
        'm_app_role_user.id as idAppRoleUser',
        'm_app_role_user.nama as appRoleUser'
      ]);
    }
    $callback = [
      'message' => 'Username / password salah!',
      'status' => 3
    ];
    foreach ($users as $user) {
      if(password_verify($password, $user->password)) {
        $callback = [
          'id' => $user->id,
          'username' => $username,
          'password' => $user->password,
          'idAppRoleUser' => $user->idAppRoleUser,
          'appRoleUser' => $user->appRoleUser,
          'message' => "Selamat datang $user->nama.",
          'status' => 2
        ];
      }
    }
    return $this->encrypt('sidak.bkpsdmsitubondokab', json_encode($callback));
  }

  public function fogetPassword(Request $request) {
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->message);
    $message = json_decode($message, true);
    $username = $message['username'];
    $nik = $message['nik'];
    $users = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->where([
      ['m_pegawai.nip', '=', $username],
      ['ktp', '=', $nik]
    ])->get([
      'm_pegawai.nip as nip',
      'm_pegawai.password as password',
      'm_data_pribadi.nama as nama',
      'm_data_pribadi.ktp as nik'
    ]);
    $callback = [
      'message' => 'NIP/NIK salah.',
      'status' => 3
    ];
    if(count($users) == 1) {
      $pwd = password_hash('12344321', PASSWORD_DEFAULT);
      DB::table('m_pegawai')->where([
        'nip' => $username
      ])->update([
        'password' => $pwd
      ]);
      $callback = [
        'message' => "Password telah berhasil direset.",
        'status' => 2
      ];
    }
    return $this->encrypt('sidak.bkpsdmsitubondokab', json_encode($callback));
  }

  public function changePassword(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if (!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $username = $message['username'];
    $newPassword = $message['newPassword'];
    $callback = [
      'message' => 'Terjadi kesalahan server.',
      'status' => 3
    ];
    if (str_contains($username, 'admin')) {
      $users = DB::table('m_admin')->where([
        ['m_admin.username', '=', $username]
      ])->get();
      if(count($users) == 1) {
        $pwd = password_hash($newPassword, PASSWORD_DEFAULT);
        DB::table('m_admin')->where([
          'username' => $username
        ])->update([
          'password' => $pwd
        ]);
        $users = DB::table('m_admin')->join('m_app_role_user', 'm_admin.idAppRoleUser', '=', 'm_app_role_user.id')->where([
          ['username', '=', $username]
        ])->get([
          'm_admin.id as id',
          'm_admin.username as username',
          'm_admin.password as password',
          'm_admin.username as nama',
          'm_app_role_user.id as idAppRoleUser',
          'm_app_role_user.nama as appRoleUser'
        ]);
        foreach ($users as $user) {
          $callback = [
            'id' => $user->id,
            'username' => $username,
            'password' => $user->password,
            'idAppRoleUser' => $user->idAppRoleUser,
            'appRoleUser' => $user->appRoleUser,
            'message' => "Password telah berhasil diubah.",
            'status' => 2
          ];
        }
      }
    } else {
      $users = DB::table('m_pegawai')->where([
        ['m_pegawai.nip', '=', $username]
      ])->get();
      if(count($users) == 1) {
        $pwd = password_hash($newPassword, PASSWORD_DEFAULT);
        DB::table('m_pegawai')->where([
          'nip' => $username
        ])->update([
          'password' => $pwd
        ]);
        $users = DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_app_role_user', 'm_pegawai.idAppRoleUser', '=', 'm_app_role_user.id')->where([
          ['m_pegawai.nip', '=', $username]
        ])->get([
          'm_pegawai.id as id',
          'm_pegawai.nip as nip',
          'm_pegawai.password as password',
          'm_data_pribadi.nama as nama',
          'm_app_role_user.id as idAppRoleUser',
          'm_app_role_user.nama as appRoleUser'
        ]);
        foreach ($users as $user) {
          $callback = [
            'id' => $user->id,
            'username' => $username,
            'password' => $user->password,
            'idAppRoleUser' => $user->idAppRoleUser,
            'appRoleUser' => $user->appRoleUser,
            'message' => "Password telah berhasil diubah.",
            'status' => 2
          ];
        }
      }
    }
    return $this->encrypt($username, json_encode($callback));
  }
}
