<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JabatanUnitOrganisasiController extends Controller
{
  ////// UNIT ORGANISASI
  public function getUnitOrganisasi($kodeKomponen = NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($kodeKomponen == NULL) {
      $data = DB::table('m_unit_organisasi')->orderBy('kodeKomponen', 'asc')->get([
        'id',
        'nama',
        'kodeKomponen'
      ]);
    } else {
      if (substr($kodeKomponen, -1) == ".") {
        $data = DB::table('m_unit_organisasi')->where([
          ['kodeKomponen', 'LIKE', $kodeKomponen.'_']
        ])->orWhere([
          ['kodeKomponen', 'LIKE', $kodeKomponen.'__']
        ])->orWhere([
          ['kodeKomponen', 'LIKE', $kodeKomponen.'___']
        ])->orderBy('kodeKomponen', 'asc')->get([
          'id',
          'nama',
          'kodeKomponen'
        ]);
      } else {
        $data = DB::table('m_unit_organisasi')->where([
          ['kodeKomponen', '=', $kodeKomponen]
        ])->orderBy('kodeKomponen', 'asc')->get([
          'id',
          'nama',
          'kodeKomponen'
        ]);
      }
    }
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertUnitOrganisasi(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_unit_organisasi')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'kodeKomponen' => $message['kodeKomponen'],
      'digunakanSotkSekarang' => $message['digunakanSotkSekarang'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = 'ditambahkan';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateUnitOrganisasi($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_unit_organisasi')->where([
      ['id', '=', $id]
    ])->update([
      'nama' => $message['nama'],
      'kodeKomponen' => $message['kodeKomponen']
    ]);
    $method = 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function deleteUnitOrganisasi($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_unit_organisasi')->where([
      ['id', '=', $id]
    ])->delete();
    $method = 'dihapus';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getHasSubOrganisasi($kodeKomponen, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_unit_organisasi')->where([
      ['kodeKomponen', 'LIKE', $kodeKomponen.'_']
    ])->orWhere([
      ['kodeKomponen', 'LIKE', $kodeKomponen.'__']
    ])->orWhere([
      ['kodeKomponen', 'LIKE', $kodeKomponen.'___']
    ])->orderBy('kodeKomponen', 'asc')->get([
      'id',
      'nama',
      'kodeKomponen'
    ]);
    $callback = [
      'message' => count($data) > 0 ? true : false,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  ////// JABATAN
  public function getUangKinerja(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_uang_kinerja')->get();
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateUangKinerja($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_uang_kinerja')->where([
      ['id', '=', $id]
    ])->update([
      'nominal' => $message['nominal']
    ]);
    $method = 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function deleteUangKinerja($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_uang_kinerja')->where([
      ['id', '=', $id]
    ])->delete();
    $method = 'dihapus';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertUangKinerja(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_uang_kinerja')->insert([
      'id' => NULL,
      'nominal' => $message['nominal'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = 'ditambahkan';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getKelasJabatan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_kelas_jabatan')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->get([
      'm_kelas_jabatan.id as id',
      'm_kelas_jabatan.nama as kelasJabatan',
      'm_uang_kinerja.nominal as uangKinerja'
    ]);
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateKelasJabatan($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_kelas_jabatan')->where([
      ['id', '=', $id]
    ])->update([
      'nama' => $message['nama'],
      'idUangKinerja' => $message['idUangKinerja']
    ]);
    $method = 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function deleteKelasJabatan($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_kelas_jabatan')->where([
      ['id', '=', $id]
    ])->delete();
    $method = 'dihapus';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertKelasJabatan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_kelas_jabatan')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'idUangKinerja' => $message['idUangKinerja'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = 'ditambahkan';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function getJabatan($kodeKomponen=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($kodeKomponen == NULL) {
      $data = DB::table('m_jabatan')->join('m_kelas_jabatan', 'm_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->get([
        'm_jabatan.id as id',
        'm_jabatan.nama as jabatan',
        'm_jabatan.kebutuhan as kebutuhan',
        'm_jabatan.target as target',
        'm_jabatan.kodeKomponen as kodeKomponen',
        'm_kelas_jabatan.nama as kelasJabatan',
        'm_uang_kinerja.nominal as uangKinerja'
      ]);
    } else {
      $data = DB::table('m_jabatan')->join('m_kelas_jabatan', 'm_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->where([
        ['m_jabatan.kodeKomponen', 'LIKE', $kodeKomponen]
      ])->get([
        'm_jabatan.id as id',
        'm_jabatan.nama as jabatan',
        'm_jabatan.kebutuhan as kebutuhan',
        'm_jabatan.target as target',
        'm_jabatan.kodeKomponen as kodeKomponen',
        'm_kelas_jabatan.nama as kelasJabatan',
        'm_uang_kinerja.nominal as uangKinerja'
      ]);
    }
    $callback = [
      'message' => $data,
      'status' => 1
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateJabatan($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_jabatan')->where([
      ['id', '=', $id]
    ])->update([
      'nama' => $message['nama'],
      'kebutuhan' => $message['kebutuhan'],
      'idKelasJabatan' => $message['idKelasJabatan'],
      'target' => $message['target'],
      'kodeKomponen' => $message['kodeKomponen']
    ]);
    $method = 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function deleteJabatan($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = DB::table('m_jabatan')->where([
      ['id', '=', $id]
    ])->delete();
    $method = 'dihapus';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertJabatan(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $data = DB::table('m_jabatan')->insert([
      'id' => NULL,
      'nama' => $message['nama'],
      'kebutuhan' => $message['kebutuhan'],
      'idKelasJabatan' => $message['idKelasJabatan'],
      'target' => $message['target'],
      'kodeKomponen' => $message['kodeKomponen'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = 'ditambahkan';
    $callback = [
      'message' => $data == 1 ? "Data berhasil $method." : "Data gagal $method.",
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
