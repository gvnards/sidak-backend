<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
