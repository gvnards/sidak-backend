<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DataPegawaiController extends Controller
{
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
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
