<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiSiasnController extends Controller
{
  function getAuthorizationToken() {
    $response = Http::asForm()->withBasicAuth('5JsEkdA7pWuuVJi8QIqboD_IeDEa', 'egPz9sSf34Q2_tD73YmjHEXNATEa')->post('https://apimws.bkn.go.id/oauth2/token', [
      'grant_type' => 'client_credentials'
    ]);
    return json_decode($response, true);
  }

  function getAuthToken() {
    $response = Http::asForm()->post('https://sso-siasn.bkn.go.id/auth/realms/public-siasn/protocol/openid-connect/token', [
      'client_id' => 'situbndoservice',
      'grant_type' => 'password',
      'username' => '199706172020121007',
      'password' => 'Alhamdulillah17'
    ]);
    return json_decode($response, true);
  }

  function getAllToken() {
    date_default_timezone_set("Asia/Jakarta");
    $currentToken = json_decode(json_encode(DB::table('api_siasn_token')->get()), true);
    $auth = '';
    $authorization = '';
    if (count($currentToken) > 0) {
      $auth = $currentToken[0]['auth'];
      $authorization = $currentToken[0]['authorization'];
      $currentTime = strtotime(date('H:i:s'));
      $existTime = strtotime(date($currentToken[0]['created_at']));
      if ((round(abs($existTime - $currentTime))/60) < 30) {
        return [
          'Auth' => 'bearer '.$auth,
          'Authorization' => 'Bearer '.$authorization
        ];
      }
    }
    $authorization = $this->getAuthorizationToken()['access_token'];
    $auth = $this->getAuthToken()['access_token'];
    if (count($currentToken) > 0) {
      DB::table('api_siasn_token')->update([
        'auth' => $auth,
        'authorization' => $authorization,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    } else {
      DB::table('api_siasn_token')->insert([
        'auth' => $auth,
        'authorization' => $authorization,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
    }
    return [
      'Auth' => 'bearer '.$auth,
      'Authorization' => 'Bearer '.$authorization
    ];
  }

  function initialUrl() {
    return 'https://apimws.bkn.go.id:8243/apisiasn/1.0';
  }

  // DIKLAT dan KURSUS -- (DIKLAT (Khusus Diklat Struktural) dan KURSUS (Selain Diklat Struktural))
  function getRiwayatDiklatASNDetail(Request $request, $idRiwayatDiklat) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /diklat/id/{idRiwayatDiklat}
    $url = $this->initialUrl() . "/diklat/id/$idRiwayatDiklat";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function getRiwayatDiklatASN(Request $request, $nipBaru) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /pns/rw-diklat/{nipBaru}
    $url = $this->initialUrl() . "/pns/rw-diklat/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function insertRiwayatDiklatASN(Request $request, $data) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /diklat/save
    // "id" dan "path", tidak perlu diisi dulu tidak masalah
    // untuk "path", itu harus upload dokumen terlebih dahulu, nanti kita dapat callback dari dokumennya,
    // lalu dari callback dokumen, nanti ditaruh di path
    $url = $this->initialUrl() . "/diklat/save";
    $response = Http::withHeaders($token)->post($url, [
      // 'id'=> 'string',
      // 'path'=> [
      //   'dok_id'=> 'string',
      //   'dok_nama'=> 'string',
      //   'dok_uri'=> 'string',
      //   'object'=> 'string',
      //   'slug'=> 'string'
      // ],
      'bobot'=> 0,
      'institusiPenyelenggara'=> $data['institusiPenyelenggara'],
      'jenisKompetensi'=> '',
      'jumlahJam'=> intval($data['jumlahJam']),
      'latihanStrukturalId'=> $data['latihanStrukturalId'],
      'nomor'=> $data['nomor'],
      'pnsOrangId'=> $data['pnsOrangId'],
      'tahun'=> intval($data['tahun']),
      'tanggal'=> $data['tanggal'],
      'tanggalSelesai'=> $data['tanggalSelesai']
    ]);
    return json_decode($response, true);
  }
  function getRiwayatKursusASNDetail(Request $request, $idRiwayatKursus) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /kursus/id/{idRiwayatKursus}
    $url = $this->initialUrl() . "/kursus/id/$idRiwayatKursus";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function getRiwayatKursusASN(Request $request, $nipBaru) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /pns/rw-kursus/{nipBaru}
    $url = $this->initialUrl() . "/pns/rw-kursus/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
function insertRiwayatKursusASN(Request $request, $data) {
  $authenticated = $this->isAuth($request)['authenticated'];
  $username = $this->isAuth($request)['username'];
  if(!$authenticated) return $this->encrypt($username, json_encode([
    'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
    'status' => $authenticated === true ? 1 : 0
  ]));
  $token = $this->getAllToken();
  // format url --> /kursus/save
  // "id" dan "path", tidak perlu diisi dulu tidak masalah
  // untuk "path", itu harus upload dokumen terlebih dahulu, nanti kita dapat callback dari dokumennya,
  // lalu dari callback dokumen, nanti ditaruh di path
  $url = $this->initialUrl() . "/kursus/save";
  $response = Http::withHeaders($token)->post($url, [
    // 'id' => 'string',
    // 'path' => [
    //   'dok_id' => 'string',
    //   'dok_nama' => 'string',
    //   'dok_uri' => 'string',
    //   'object' => 'string',
    //   'slug' => 'string'
    // ],
    'lokasiId' => '',
    'instansiId' => 'A5EB03E2421DF6A0E040640A040252AD',
    'institusiPenyelenggara' => $data['institusiPenyelenggara'],
    'jenisDiklatId' => $data['jenisDiklatId'],
    'jenisKursus' => $data['jenisKursus'],
    'jenisKursusSertipikat' => $data['jenisKursusSertipikat'],
    'jumlahJam' => intval($data['jumlahJam']),
    'namaKursus' => $data['namaKursus'],
    'nomorSertipikat' => $data['nomorSertipikat'],
    'pnsOrangId' => $data['pnsOrangId'],
    'tahunKursus' => intval($data['tahunKursus']),
    'tanggalKursus' => $data['tanggalKursus'],
    'tanggalSelesaiKursus' => $data['tanggalSelesaiKursus']
  ]);
  return json_decode($response, true);
}
  // UPLOAD -- belum sama sekali
  // JABATAN --
  function getRiwayatJabatanASNDetail(Request $request, $idRiwayatJabatan) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /jabatan/id/{idRiwayatJabatan}
    $url = $this->initialUrl() . "/jabatan/id/$idRiwayatJabatan";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function getRiwayatJabatanASN(Request $request, $nipBaru) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /jabatan/pns/{nipBaru}
    $url = $this->initialUrl() . "/jabatan/pns/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function insertRiwayatJabatanASN(Request $request, $data) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /jabatan/save
    // "id" dan "path", tidak perlu diisi dulu tidak masalah
    // untuk "path", itu harus upload dokumen terlebih dahulu, nanti kita dapat callback dari dokumennya,
    // lalu dari callback dokumen, nanti ditaruh di path
    $url = $this->initialUrl() . "/jabatan/save";
    $response = Http::withHeaders($token)->post($url, [
      'eselonId' => $data['eselonId'],
      // 'id' => '',
      'instansiId' => 'A5EB03E23CD4F6A0E040640A040252AD',
      'jabatanFungsionalId' => $data['jenisJabatan'] == '2' ? $data['jabatanId'] : '',
      'jabatanFungsionalUmumId' => $data['jenisJabatan'] == '4' ? $data['jabatanId'] : '',
      'jenisJabatan' => $data['jenisJabatan'],
      'nomorSk' => $data['nomorSk'],
      // 'path' => [
      //   'dok_id' => '',
      //   'dok_nama' => '',
      //   'dok_uri' => '',
      //   'object' => '',
      //   'slug' => ''
      // ],
      'pnsId' => $data['pnsId'],
      'satuanKerjaId' => 'A5EB03E2421DF6A0E040640A040252AD',
      'tanggalSk' => $data['tanggalSk'],
      'tmtJabatan' => $data['tmtJabatan'],
      'tmtPelantikan' => $data['tmtPelantikan'],
      'unorId' => $data['unorId']
    ]);
    return json_decode($response, true);

    ///////////// return success
    // {
    //   "success": true,
    //   "mapData": {
    //       "rwJabatanId": "d1120c07-fb3b-11ed-a270-0a580a830052"
    //   },
    //   "message": "success"
    // }
    ///////////// return failed (jika field mandatory isinya salah atau isinya tidak ada di table referensi)
    // {
    //   "success": false,
    //   "mapData": null,
    //   "message": "Jenis Jabatan fungsional tertentu, namun jabatan yang dimasukkan tidak ditemukan pada data referensi"
    // }
    /////////// return error (jika field mandatory tidak terisi)
    // {
    //   "code": 0,
    //   "data": null,
    //   "message": "5 errors occurred:\n\t* UnorID is required\n\t* InstansiKerjaID is required\n\t* PnsOrangId is required\n\t* NomorSk is required\n\t* SatuanKerjaId is required\n\n"
    // }
  }

  // GOLONGAN
  function getRiwayatPangkatGolonganASN(Request $request, $nipBaru) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /pns/rw-golongan/{nipBaru}
    $url = $this->initialUrl() . "/pns/rw-golongan/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }

  // PENDIDIKAN
  function getRiwayatPendidikanASN(Request $request, $nipBaru) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $token = $this->getAllToken();
    // format url --> /pns/rw-pendidikan/{nipBaru}
    $url = $this->initialUrl() . "/pns/rw-pendidikan/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
}
