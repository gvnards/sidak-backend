<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataHukumanDisiplinController extends Controller
{
  public function getJenisHukdis(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = DB::table('m_jenis_hukuman_disiplin')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function getDaftarHukdis($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = DB::table('m_daftar_hukuman_disiplin')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function getDasarHukumHukdis(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = DB::table('m_daftar_dasar_hukum_hukuman_disiplin')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function getDaftarAlasanHukdis(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $data = DB::table('m_daftar_alasan_hukuman_disiplin')->get();
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function getDataHukdis($idPegawai, $idDataHukdis=null, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if($idDataHukdis === null) {
      $data = DB::table('m_pegawai')->join('m_data_hukuman_disiplin', 'm_pegawai.id', '=', 'm_data_hukuman_disiplin.idPegawai')->join('m_jenis_hukuman_disiplin', 'm_data_hukuman_disiplin.idJenisHukumanDisiplin', '=', 'm_jenis_hukuman_disiplin.id')->join('m_daftar_hukuman_disiplin', 'm_data_hukuman_disiplin.idDaftarHukumanDisiplin', '=', 'm_daftar_hukuman_disiplin.id')->whereIn('m_data_hukuman_disiplin.idUsulanStatus', [3, 4])->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_hukuman_disiplin.idUsulanHasil', '=', 1],
        ['m_data_hukuman_disiplin.idUsulan', '=', 1],
      ])->get([
        'm_data_hukuman_disiplin.id',
        'm_jenis_hukuman_disiplin.nama as jenisHukumanDisiplin',
        'm_daftar_hukuman_disiplin.nama as daftarHukumanDisiplin'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_hukuman_disiplin', 'm_pegawai.id', '=', 'm_data_hukuman_disiplin.idPegawai')->leftJoin('m_dokumen', 'm_data_hukuman_disiplin.idDokumen', '=', 'm_dokumen.id')->where([
        ['m_data_hukuman_disiplin.id', '=', $idDataHukdis],
      ])->get([
        'm_data_hukuman_disiplin.*'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'pangkat', 'pdf');
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $callback;
  }

  public function insertDataHukdis($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_hukuman_disiplin')->where([
        ['idDataHukumanDisiplinUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return [
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ];
      }
    }
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_HUKUMAN_DISIPLIN_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_HUKUMAN_DISIPLIN_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'hukdis');

    $data = DB::table('m_data_hukuman_disiplin')->insert([
      'id' => NULL,
      'idJenisHukumanDisiplin' => $message['idJenisHukumanDisiplin'],
      'idDaftarHukumanDisiplin' => $message['idDaftarHukumanDisiplin'],
      'nomorDokumen' => $message['nomorDokumen'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'tmtAwal' => $message['tmtAwal'],
      'masaHukuman' => $message['masaHukuman'],
      'tmtAkhir' => $message['tmtAkhir'],
      'idDaftarDasarHukumHukdis' => $message['idDaftarDasarHukumHukdis'],
      'idDaftarAlasanHukdis' => $message['idDaftarAlasanHukdis'],
      'keteranganAlasanHukdis' => $message['keteranganAlasanHukdis'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataHukumanDisiplinUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $callback;
  }
}
