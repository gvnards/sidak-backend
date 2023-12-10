<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataCpnsPnsController extends Controller
{
  public function getDataCpnsPns($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $data = json_decode(DB::table('m_pegawai')->join('m_data_cpns_pns', 'm_pegawai.id', '=', 'm_data_cpns_pns.idPegawai')->where([
      ['m_pegawai.id', '=', $idPegawai]
    ])->get([
      'm_data_cpns_pns.*'
    ]), true);
    $data[0]['dokumenSkCpns'] = $this->getBlobDokumen($data[0]['idDokumenSkCpns'], 'cpns', 'pdf');
    $data[0]['dokumenSkPns'] = $this->getBlobDokumen($data[0]['idDokumenSkPns'], 'pns', 'pdf');
    $callback = [
      'message' => count($data) == 1 ? $data : 'Data tidak ditemukan.',
      'status' => count($data) == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateDataCpnsPns($id, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $dataCpnsPns = json_decode(DB::table('m_data_cpns_pns')->where([
      ['id', '=', $id]
    ])->get(),true);
    $idPegawai = $dataCpnsPns[0]['idPegawai'];
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $idPegawai]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $idDokumenSkCpns = $message['idDokumenSkCpns'];
    $idDokumenSkPns = $message['idDokumenSkPns'];
    $skCpns_ = $message['dokumenSkCpns'];
    $skPns_ = $message['dokumenSkPns'];
    if ($skCpns_ != '') {
      if ($idDokumenSkCpns != null) {
        DB::table('m_data_cpns_pns')->where([
          ['id', '=', $id]
        ])->update([
          'idDokumenSkPns' => NULL
        ]);
        $this->deleteDokumen($idDokumenSkPns, 'cpns', 'pdf');
      }
      $idDokumenSkCpns = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_SK_CPNS_$nip",
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
      $this->uploadDokumen("DOK_SK_CPNS_".$nip, $skCpns_, 'pdf', 'cpns');
    }
    if ($skPns_ != '') {
      if ($idDokumenSkPns != null) {
        DB::table('m_data_cpns_pns')->where([
          ['id', '=', $id]
        ])->update([
          'idDokumenSkPns' => NULL
        ]);
        $this->deleteDokumen($idDokumenSkPns, 'pns', 'pdf');
      }
      $idDokumenSkPns = DB::table('m_dokumen')->insertGetId([
        'id' => NULL,
        'nama' => "DOK_SK_PNS_$nip",
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]);
      $this->uploadDokumen("DOK_SK_PNS_".$nip, $skPns_, 'pdf', 'pns');
    }
    $data = DB::table('m_data_cpns_pns')->where([
      ['id', '=', $id]
    ])->update([
      'tmtCpns' => $message['tmtCpns'],
      'tglSkCpns' => $message['tglSkCpns'],
      'nomorSkCpns' => $message['nomorSkCpns'],
      'tglSpmt' => $message['tglSpmt'],
      'nomorSpmt' => $message['nomorSpmt'],
      'idPejabatPengangkatCpns' => $message['idPejabatPengangkatCpns'],
      'idDokumenSkCpns' => $idDokumenSkCpns,
      'tmtPns' => $message['tmtPns'],
      'tglSkPns' => $message['tglSkPns'],
      'nomorSkPns' => $message['nomorSkPns'],
      'tglSttpl' => $message['tglSttpl'],
      'nomorSttpl' => $message['nomorSttpl'],
      'tglSuratDokter' => $message['tglSuratDokter'],
      'nomorSuratDokter' => $message['nomorSuratDokter'],
      'nomorKarpeg' => $message['nomorKarpeg'],
      'nomorKarisKarsu' => $message['nomorKarisKarsu'],
      'idDokumenSkPns' => $idDokumenSkPns,
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $callback = [
      'message' => $data == 1 ? 'Data berhasil disimpan.' : 'Data gagal disimpan.',
      'status' => $data == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
