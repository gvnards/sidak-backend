<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataShortBriefController extends Controller
{
  public function getDataShortBrief($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('v_short_brief')->where([
      ['v_short_brief.id', '=', $id],
    ])->get();
    $callback = [
      'message' => count($data) == 1 ? $data : 'Data tidak ditemukan.',
      'status' => count($data) == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
    return $data;
  }
}
