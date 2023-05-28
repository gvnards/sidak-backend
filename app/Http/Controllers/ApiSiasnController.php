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

  // DIKLAT -- belum sama sekali
  // UPLOAD -- belum sama sekali
  // JABATAN --
  function getDetailJabatanASN(Request $request, $idRiwayatJabatan) {
    $token = $this->getAllToken();
    // format url --> /jabatan/id/{idRiwayatJabatan}
    $url = $this->initialUrl() . "/jabatan/id/$idRiwayatJabatan";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function getDataJabatanASN(Request $request, $nipBaru) {
    $token = $this->getAllToken();
    // format url --> /jabatan/pns/{nipBaru}
    $url = $this->initialUrl() . "/jabatan/pns/$nipBaru";
    $response = Http::withHeaders($token)->get($url, []);
    return json_decode($response, true);
  }
  function insertDataJabatanASN(Request $request) {
    $token = $this->getAllToken();
    // format url --> /jabatan/save
    // "id" dan "path", tidak perlu diisi dulu tidak masalah
    // untuk "path", itu harus upload dokumen terlebih dahulu, nanti kita dapat callback dari dokumennya,
    // lalu dari callback dokumen, nanti ditaruh di path
    $url = $this->initialUrl() . "/jabatan/save";
    // $response = Http::withHeaders($token)->post($url, [
    //   'eselonId' => '',
    //   // 'id' => '',
    //   'instansiId' => 'A5EB03E23CD4F6A0E040640A040252AD',
    //   'jabatanFungsionalId' => 'A5EB03E23EB1F6A0E040640A040252AD',
    //   'jabatanFungsionalUmumId' => '',
    //   'jenisJabatan' => '2',
    //   'nomorSk' => 'coba',
    //   // 'path' => [
    //   //   'dok_id' => '',
    //   //   'dok_nama' => '',
    //   //   'dok_uri' => '',
    //   //   'object' => '',
    //   //   'slug' => ''
    //   // ],
    //   'pnsId' => '7E85A2741FFFBD8DE050640A3C036B36',
    //   'satuanKerjaId' => 'A5EB03E2421DF6A0E040640A040252AD',
    //   'tanggalSk' => '26-05-2023',
    //   'tmtJabatan' => '26-05-2023',
    //   'tmtPelantikan' => '26-05-2023',
    //   'unorId' => '8ae483a686483d1901864868a4bb0260'
    // ]);
    // return json_decode($response, true);

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
}
