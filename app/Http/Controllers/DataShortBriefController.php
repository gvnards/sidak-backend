<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class DataShortBriefController extends Controller
{
  public function getDataShortBrief($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = json_decode(DB::table('v_short_brief')->where([
      ['v_short_brief.id', '=', $id],
    ])->get(), true);
    $data[0]['foto'] = $this->getPhoto($data[0]['nip']);
    // $data[0]['foto'] = '';
    $callback = [
      'message' => count($data) == 1 ? $data : 'Data tidak ditemukan.',
      'status' => count($data) == 1 ? 2 : 3
    ];
    return $callback;
  }
  private function getPhoto($nip) {
    $namaFoto = $nip;
    $blob = '';

    $listExtensi = ['jpg', 'jpeg', 'png'];
    foreach ($listExtensi as $extensi) {
      try {
        $path = storage_path('app/foto/'.$namaFoto.".".$extensi);
        $fileSize = filesize($path);
        // $mimeType = mime_content_type($path);
        $mimeType = 'image/'.($extensi === 'jpg' ? 'jpeg' : $extensi);

        if ($fileSize > 512000) {
          $this->compressPhoto($path, $path, 75, $mimeType);
        }
        $getPdfFromServerFolder = base64_encode(file_get_contents($path));
        $blob = 'data:'.$mimeType.';base64,'.$getPdfFromServerFolder;
        break;
      } catch (Exception $ex) {}
    }
    // imagejpeg(imagecreatefromjpeg($path), $path, 75);
    return $blob;
  }
  private function compressPhoto($source, $destination, $quality, $mime) {
    // $info = getimagesize($source);

    if ($mime == 'image/jpeg') $image = imagecreatefromjpeg($source);
    elseif ($mime == 'image/gif') $image = imagecreatefromgif($source);
    elseif ($mime == 'image/png') $image = imagecreatefrompng($source);

    imagejpeg($image, $destination, $quality);
    return $destination;
  }
  public function changePhoto(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $message = json_decode($this->decrypt($username, $request->message), true);
    $listExtensi = ['jpg', 'jpeg', 'png'];
    foreach ($listExtensi as $extensi) {
      try {
        Storage::delete('foto/'.$message['nip'].'.'.$extensi);
      } catch (Exception $ex) {}
    }
    Storage::putFileAs('foto', $message['photo'], $message['nip'].'.'.'jpg');
    return [
      'message' => 'Foto berhasil diperbaharui',
      'status' => 2
    ];
  }
}
