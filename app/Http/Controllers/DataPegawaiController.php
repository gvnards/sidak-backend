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
  public function checkPegawai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    $message = json_decode($this->decrypt($username, $request->message), true);
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];
    $nips = json_decode(DB::table('m_pegawai')->get([
      'nip'
    ]), true);
    $nipDicari = []; //// KUMPULAN NIP DARI SIASN YG BELUM ADA DI SIDAK
    for ($i=0; $i < count($message); $i++) { //// LOOP FOR NIP SIASN
      $isAny = false;
      for ($j=0; $j < count($nips); $j++) { //// LOOP FOR NIP SIDAK
        if ($message[$i] == $nips[$j]['nip']) {
          $isAny = true;
          break;
        }
      }
      array_push($nipDicari, [
        'nip' => $message[$i],
        'exist' => $isAny,
        'processed' => [
          'isProcessed' => false,
          'message' => '',
          'status' => 3,
        ]
      ]);
    }
    return [
      'status' => 2,
      'message' => $nipDicari
    ];
  }
  public function addPegawai(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    $message = json_decode($this->decrypt($username, $request->message), true);
    if(!$authenticated) return [
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ];

    $response = (new ApiSiasnController)->getDataUtamaASN($request, $message);
    if ($response['data'] === 'Data tidak ditemukan') {
      return [
        'message' => 'Pegawai tidak ada',
        'status' => 3
      ];
    } else if ($response['data']['tmtPensiun'] != null) {
      return [
        'message' => 'Pegawai tidak ada',
        'status' => 3
      ];
    }
    //// INSERT TO DATABASE
    $response = $response['data'];
    $pwd = password_hash('12344321', PASSWORD_DEFAULT);
    $idPegawai = DB::table('m_pegawai')->insertGetId([
      'id' => NULL,
      'nip' => $message,
      'password' => $pwd,
      'idAppRoleUser' => 4,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'idBkn' => $response['id']
    ]);
    DB::table('m_data_pribadi')->insert([
      'id' => NULL,
      'nama' => $response['nama'],
      'tempatLahir' => $response['tempatLahir'],
      'tanggalLahir' => date('Y-m-d', strtotime($response['tglLahir'])),
      'alamat' => $response['alamat'],
      'ktp' => $response['nik'],
      'nomorHp' => $response['noHp'],
      'email' => $response['email'],
      'npwp' => $response['noNpwp'],
      'bpjs' => $response['bpjs'],
      'idPegawai' => $idPegawai,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    DB::table('m_data_cpns_pns')->insert([
      'id' => NULL,
      'idPegawai' => $idPegawai,
      'tmtCpns' => $response['tmtCpns'] == null || $response['tmtCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tmtCpns'])),
      'tglSkCpns' => $response['tglSkCpns'] == null || $response['tglSkCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkCpns'])),
      'nomorSkCpns' => $response['nomorSkCpns'],
      'tglSpmt' => $response['tglSkCpns'] == null || $response['tglSkCpns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkCpns'])),
      'nomorSpmt' => $response['noSpmt'],
      'idPejabatPengangkatCpns' => 1,
      'idDokumenSkCpns' => NULL,
      'tmtPns' => $response['tmtPns'] == null || $response['tmtPns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tmtPns'])),
      'tglSkPns' => $response['tglSkPns'] == null || $response['tglSkPns'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSkPns'])),
      'nomorSkPns' => $response['nomorSkPns'],
      'tglSttpl' => $response['tglSttpl'] == null || $response['tglSttpl'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSttpl'])),
      'nomorSttpl' => $response['nomorSttpl'],
      'tglSuratDokter' => $response['tglSuratKeteranganDokter'] == null || $response['tglSuratKeteranganDokter'] == '' ? '0000-00-00' : date('Y-m-d', strtotime($response['tglSuratKeteranganDokter'])),
      'nomorSuratDokter' => $response['noSuratKeteranganDokter'],
      'nomorKarpeg' => $response['noSeriKarpeg'],
      'nomorKarisKarsu' => '',
      'idDokumenSkPns' => NULL,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $tempNip = "".$message[12].$message[13];
    DB::table('m_data_status_kepegawaian')->insert([
      'id' => NULL,
      'idPegawai' => $idPegawai,
      'idDaftarStatusKepegawaian' => (intval($tempNip) === 21) ? 15 : 4,
      'tmt' => Carbon::now()->format('Y-m-d'),
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    /// SYNC DATA
    (new ApiSiasnSyncController)->syncPendidikanASN($request, $idPegawai);
    (new ApiSiasnSyncController)->syncPangkatGolonganASN($request, $idPegawai);
    (new ApiSiasnSyncController)->syncJabatanASN($request, $idPegawai);

    return [
      'status' => 2,
      'message' => 'Pegawai berhasil ditambahkan'
    ];
  }
}
