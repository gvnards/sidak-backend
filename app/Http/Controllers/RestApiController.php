<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RestApiController extends Controller
{
  function cobaPost() {
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
          'auth' => $auth,
          'authorization' => $authorization
        ];
      }
    }
    // $authorization = $this->getAuthorizationToken()['access_token'];
    // $auth = $this->getAuthToken()['access_token'];
    $auth = 'updated';
    $authorization = 'updated';
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
      'auth' => $auth,
      'authorization' => $authorization
    ];
  }
}
