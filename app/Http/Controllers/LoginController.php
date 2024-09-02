<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class LoginController extends ApiSiasnController
{
  public function login(Request $request) {
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $request->message);
    $message = json_decode($message, true);
    $username = $message['username'];
    $password = $message['password'];
    $users = [];
    if(!str_contains($username, 'admin')) {
      $usersTemp = json_decode(DB::table('m_pegawai')->where([
        ['nip', '=', $username]
      ])->get()->toJson(), true);
      if (count($usersTemp) === 0) {
        $responseGetAuth = $this->getAuthToken($username, $password);
        if(!isset($responseGetAuth['access_token'])) {
          return [
            'message' => 'Username / password salah!',
            'status' => 3
          ];
        }
        $response = $this->getDataUtamaASN($request, $username);
        if ($response['data'] === 'Data tidak ditemukan') {
          return [
            'message' => 'Username / password salah!',
            'status' => 3
          ];
        } else if ($response['data']['tmtPensiun'] != null) {
          return [
            'message' => 'Username / password salah!',
            'status' => 3
          ];
        }
        $response = $response['data'];
        $newPassword = password_hash($password, PASSWORD_DEFAULT);
        $idPegawai = DB::table('m_pegawai')->insertGetId([
          'id' => NULL,
          'nip' => $username,
          'password' => $newPassword,
          'idAppRoleUser' => 4,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'idBkn' => $response['id']
        ]);
        DB::table('m_data_pribadi')->insert([
          'id' => NULL,
          'nama' => $response['nama'],
          'tempatLahir' => $response['tempatLahir'],
          'tanggalLahir' => date('Y-m-d', strtotime($response['tglLahir'])),
          'alamat' => $response['alamat'],
          'ktp' => $response['nik'],
          'nomorHp' => $response['noHp'],
          'email' => $response['email'],
          'npwp' => $response['noNpwp'],
          'bpjs' => $response['bpjs'],
          'idPegawai' => $idPegawai,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $tempNip = "".$username[12].$username[13];
        DB::table('m_data_status_kepegawaian')->insert([
          'id' => NULL,
          'idPegawai' => $idPegawai,
          'idDaftarStatusKepegawaian' => (intval($tempNip) === 21) ? 15 : 4,
          'tmt' => Carbon::now()->format('Y-m-d'),
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $usersTemp = json_decode(DB::table('m_pegawai')->where([
          ['nip', '=', $username]
        ])->get()->toJson(), true);
      }
      if(count($usersTemp) === 1) {
        if (!password_verify($password, $usersTemp[0]['password'])) {
          if ($password == '12344321') {
            return [
              'message' => 'Username / password salah!',
              'status' => 3
            ];
          }
          // login ke siasn, yang akan mendapatkan access_token (jika berhasil/username dan password benar)
          $response = $this->getAuthToken($username, $password);
          if(!isset($response['access_token'])) {
            return [
              'message' => 'Username / password salah!',
              'status' => 3
            ];
          } else {
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
            DB::table('m_pegawai')->where([
              ['id', '=', $usersTemp[0]['id']]
            ])->update([
              'password' => $newPassword
            ]);
          }
        }
      } else {
        return [
          'message' => 'Username / password salah!',
          'status' => 3
        ];
      }
      array_push($users, json_decode(DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_app_role_user', 'm_pegawai.idAppRoleUser', '=', 'm_app_role_user.id')->where([
        ['m_pegawai.nip', '=', $username]
      ])->get([
        'm_pegawai.id as id',
        'm_pegawai.nip as nip',
        'm_pegawai.password as password',
        'm_data_pribadi.nama as nama',
        'm_app_role_user.id as idAppRoleUser',
        'm_app_role_user.nama as appRoleUser'
      ]), true)[0]);
    } else {
      $usersTemp = json_decode(DB::table('m_admin')->join('m_app_role_user', 'm_admin.idAppRoleUser', '=', 'm_app_role_user.id')->where([
        ['username', '=', $username]
      ])->get([
        'm_admin.id as id',
        'm_admin.username as username',
        'm_admin.password as password',
        'm_admin.username as nama',
        'm_app_role_user.id as idAppRoleUser',
        'm_app_role_user.nama as appRoleUser'
      ])->toJson(), true);
      $users = [];
      foreach ($usersTemp as $userTemp) {
        if (password_verify($password, $userTemp['password'])) {
          $users = [$userTemp];
        }
      }
      if (count($users) === 0) {
        return [
          'message' => 'Username / password salah!',
          'status' => 3
        ];
      }
    }
    $callback = [
      'message' => 'Username / password salah!',
      'status' => 3
    ];
    foreach ($users as $user) {
      $msg = [
        'tkn' => [
          'id' => $user['id'],
          'username' => $username,
          'password' => $user['password'],
          'idAppRoleUser' => $user['idAppRoleUser'],
          'appRoleUser' => $user['appRoleUser'],
        ],
        'text' => "Selamat datang ".$user['nama']."."
      ];
      $callback = [
        'message' => $this->encrypt('sidak.bkpsdmsitubondokab', json_encode($msg)),
        'status' => 2
      ];
    }
    return $callback;
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
