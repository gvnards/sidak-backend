<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataJabatanController extends Controller
{
  public function getDataJabatan($idPegawai, $idDataJabatan=null, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if($idDataJabatan === null) {
      $data = DB::table('m_pegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->where([
        ['m_pegawai.id', '=', $idPegawai],
        ['m_data_jabatan.idUsulanHasil', '=', 1],
        ['m_data_jabatan.idUsulan', '=', 1],
      ])->orderBy('m_data_jabatan.tmt', 'desc')->get([
        'm_data_jabatan.id as id',
        'm_jabatan.nama as jabatan',
        'm_jenis_jabatan.nama as jenisJabatan'
      ]);
    } else {
      $data = json_decode(DB::table('m_pegawai')->join('m_data_jabatan', 'm_pegawai.id', '=', 'm_data_jabatan.idPegawai')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_kelas_jabatan', 'm_jabatan.idKelasJabatan', '=', 'm_kelas_jabatan.id')->join('m_uang_kinerja', 'm_kelas_jabatan.idUangKinerja', '=', 'm_uang_kinerja.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_unit_organisasi', 'm_jabatan.kodeKomponen', '=', 'm_unit_organisasi.kodeKomponen')->leftJoin('m_eselon', 'm_jabatan.idEselon', '=', 'm_eselon.id')->where([
        ['m_data_jabatan.id', '=', $idDataJabatan],
      ])->get([
        'm_data_jabatan.*',
        'm_jabatan.kodeKomponen as kodeKomponen'
      ]), true);
      $data[0]['dokumen'] = $this->getBlobDokumen($data[0]['idDokumen'], 'jabatan', 'pdf');
    }
    $callback = [
      'message' => $data,
      'status' => 2
    ];
    return $this->encrypt($username, json_encode($callback));
  }

  public function insertDataJabatan($id=NULL, Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    if ($id !== NULL) {
      $countIsAny = count(json_decode(DB::table('m_data_jabatan')->where([
        ['idDataJabatanUpdate', '=', $id],
        ['idUsulanHasil', '=', 3]
      ])->get()->toJson(), true));
      if ($countIsAny > 0) {
        return $this->encrypt($username, json_encode([
          'message' => "Maaf, data sudah pernah diusulkan sebelumnya untuk perubahan.\nSilahkan menunggu data terverifikasi terlebih dahulu.",
          'status' => 3
        ]));
      }
    }
    $message = json_decode($this->decrypt($username, $request->message), true);
    $jabatanCount = json_decode(DB::table('m_jabatan')->where([
      ['id', '=', $message['idJabatan']],
      ['kodeKomponen', '=', $message['kodeKomponen']]
    ])->get()->toJson(), true);
    if (count($jabatanCount) === 0) {
      $jabatanBaru = json_decode(DB::table('m_jabatan')->where([
        ['id', '=', $message['idJabatan']]
      ])->get()->toJson(), true)[0];
      $idJabatanBaru = DB::table('m_jabatan')->insertGetId([
        'id' => NULL,
        'nama' => $jabatanBaru['nama'].' (Tidak ada di dalam Peta Jabatan Unit Organisasi)',
        'kebutuhan' => -1,
        'idKelasJabatan' => $jabatanBaru['idKelasJabatan'],
        'target' => $jabatanBaru['target'],
        'kodeKomponen' => $message['kodeKomponen'],
        'idJenisJabatan' => $jabatanBaru['idJenisJabatan'],
        'idEselon' => $jabatanBaru['idEselon'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'idBkn' => $jabatanBaru['idBkn']
      ]);
      $message['idJabatan'] = $idJabatanBaru;
    }
    $nip_ = DB::table('m_pegawai')->where([['id', '=', $message['idPegawai']]])->get();
    foreach ($nip_ as $key => $value) {
      $nip = $value->nip;
    }
    $dokumen = DB::table('m_dokumen')->insertGetId([
      'id' => NULL,
      'nama' => "DOK_SK_JABATAN_".$nip."_".$message['date'],
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $this->uploadDokumen("DOK_SK_JABATAN_".$nip."_".$message['date'],$message['dokumen'], 'pdf', 'jabatan');

    $data = DB::table('m_data_jabatan')->insert([
      'id' => NULL,
      'idJabatan' => $message['idJabatan'],
      'isPltPlh' => $message['isPltPlh'],
      'idJabatanTugasTambahan' => $message['idJabatanTugasTambahan'],
      'tmt' => $message['tmt'],
      'spmt' => $message['spmt'],
      'tanggalDokumen' => $message['tanggalDokumen'],
      'nomorDokumen' => $message['nomorDokumen'],
      'idDokumen' => $dokumen,
      'idPegawai' => $message['idPegawai'],
      'idUsulan' => $id == NULL ? 1 : 2,
      'idUsulanStatus' => 1,
      'idUsulanHasil' => 3,
      'keteranganUsulan' => '',
      'idDataJabatanUpdate' => $id,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ]);
    $method = $id == NULL ? 'ditambahkan' : 'diperbaharui';
    $callback = [
      'message' => $data == 1 ? "Data berhasil diusulkan untuk $method.\nSilahkan cek status usulan secara berkala pada Menu Usulan." : "Data gagal diusulkan untuk $method.",
      'status' => $data == 1 ? 2 : 3
    ];
    return $this->encrypt($username, json_encode($callback));
  }
}
