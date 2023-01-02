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
    $data = DB::table('m_pegawai')->join('m_data_cpns_pns', 'm_pegawai.id', '=', 'm_data_cpns_pns.idPegawai')->leftJoin('m_dokumen as dokumenSkCpns', 'm_data_cpns_pns.idDokumenSkCpns', 'dokumenSkCpns.id')->leftJoin('m_dokumen as dokumenSkPns', 'm_data_cpns_pns.idDokumenSkPns', 'dokumenSkPns.id')->where([
      ['m_pegawai.id', '=', $idPegawai]
    ])->get([
      'm_data_cpns_pns.*',
      'dokumenSkCpns.dokumen as dokumenSkCpns',
      'dokumenSkPns.dokumen as dokumenSkPns',
    ]);
    $callback = [
      'message' => count($data) == 1 ? $data : 'Data tidak ditemukan.',
      'status' => count($data) == 1 ? 1 : 0
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function updateDataCpnsPns($idPegawai, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $idPegawai]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $idDokumenSkCpns = $message['idDokumenSkCpns'];
    $idDokumenSkPns = $message['idDokumenSkPns'];
    $skCpns_ = $message['dokumenSkCpns'];
    $skPns_ = $message['dokumenSkPns'];
    $skCpns = DB::table('m_dokumen')->where([
      ['id', '=', $idDokumenSkCpns],
      ['dokumen', '=', $skCpns_]
    ])->get();
    $skPns = DB::table('m_dokumen')->where([
      ['id', '=', $idDokumenSkPns],
      ['dokumen', '=', $skPns_]
    ])->get();
    if (count($skCpns)==0 && $skCpns_!='' && $skCpns_!=null) {
      if ($idDokumenSkCpns == null) {
        $idDokumenSkCpns = DB::table('m_dokumen')->insertGetId([
          'id' => NULL,
          'nama' => "DOK_SK_CPNS_$nip",
          'dokumen' => $message['dokumenSkCpns'],
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
      } else {
        DB::table('m_dokumen')->where([
          ['id', '=', $message['idDokumenSkCpns']]
        ])->update([
          'dokumen' => $skCpns_
        ]);
      }
    }
    if (count($skPns)==0 && $skPns_!='' && $skPns_!=null) {
      if ($idDokumenSkPns == null) {
        $idDokumenSkPns = DB::table('m_dokumen')->insertGetId([
          'id' => NULL,
          'nama' => "DOK_SK_PNS_$nip",
          'dokumen' => $message['dokumenSkPns'],
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
      } else {
        DB::table('m_dokumen')->where([
          ['id', '=', $message['idDokumenSkPns']]
        ])->update([
          'dokumen' => $skPns_
        ]);
      }
    }
    $data = DB::table('m_data_cpns_pns')->where([
      ['id', '=', $idPegawai]
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
      'status' => $data
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
