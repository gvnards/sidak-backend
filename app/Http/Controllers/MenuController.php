<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
  public function getMainMenu(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $mainMenu = [];
    if(!str_contains($username, 'admin')) {
      $mainMenu = DB::table('m_pegawai')->join('m_app_role_user_mainmenu', 'm_pegawai.idAppRoleUser', '=', 'm_app_role_user_mainmenu.idAppRoleUser')->join('m_app_mainmenu', 'm_app_mainmenu.id', '=', 'm_app_role_user_mainmenu.idAppMainmenu')->where([
        ['m_pegawai.nip', '=', $username]
      ])->orderBy('m_app_mainmenu.order', 'asc')->get(
        [
          'm_app_mainmenu.id',
          'm_app_mainmenu.nama',
          'm_app_mainmenu.icon',
        ]
      );
    } else {
      $mainMenu = DB::table('m_admin')->join('m_app_role_user_mainmenu', 'm_admin.idAppRoleUser', '=', 'm_app_role_user_mainmenu.idAppRoleUser')->join('m_app_mainmenu', 'm_app_mainmenu.id', '=', 'm_app_role_user_mainmenu.idAppMainmenu')->where([
        ['m_admin.username', '=', $username]
      ])->orderBy('m_app_mainmenu.order', 'asc')->get(
        [
          'm_app_mainmenu.id as id',
          'm_app_mainmenu.nama as nama',
          'm_app_mainmenu.icon as icon',
        ]
      );
    }
    $callback = [
      'message' => count($mainMenu) == 0 ? 'Data tidak ditemukan.' : $mainMenu,
      'status' => count($mainMenu) == 0 ? 0 : 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getPegawaiMenu(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $pegawaiMenu = [];
    if(!str_contains($username, 'admin')) {
      $pegawaiMenu = DB::table('m_pegawai')->join('m_app_role_user_pegawaimenu', 'm_pegawai.idAppRoleUser', '=', 'm_app_role_user_pegawaimenu.idAppRoleUser')->join('m_app_pegawaimenu', 'm_app_pegawaimenu.id', '=', 'm_app_role_user_pegawaimenu.idAppPegawaimenu')->where([
        ['m_pegawai.nip', '=', $username],
      ])->get([
        'm_app_pegawaimenu.id as id',
        'm_app_pegawaimenu.nama as nama',
        'm_app_pegawaimenu.illustration as illustration'
      ]);
    } else {
      $pegawaiMenu = DB::table('m_admin')->join('m_app_role_user_pegawaimenu', 'm_admin.idAppRoleUser', '=', 'm_app_role_user_pegawaimenu.idAppRoleUser')->join('m_app_pegawaimenu', 'm_app_pegawaimenu.id', '=', 'm_app_role_user_pegawaimenu.idAppPegawaimenu')->where([
        ['m_admin.username', '=', $username],
      ])->get([
        'm_app_pegawaimenu.id as id',
        'm_app_pegawaimenu.nama as nama',
        'm_app_pegawaimenu.illustration as illustration'
      ]);
    }
    $callback = [
      'message' => count($pegawaiMenu) == 0 ? 'Data tidak ditemukan.' : $pegawaiMenu,
      'status' => count($pegawaiMenu) == 0 ? 0 : 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
