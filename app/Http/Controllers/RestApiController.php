<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RestApiController extends Controller
{
  public function getRestTokenAdmin(Request $request, $username) {
    $usr = $username;
    $user = json_decode(DB::table('m_admin')->where([
      ['username', '=', $usr]
    ])->get(), true)[0];
    $callback = [
      'id' => $user['id'],
      'username' => $user['username'],
      'password' => $user['password']
    ];
    $token = $this->encrypt('sidak.bkpsdmsitubondokab', $callback);
    $token = str_replace("{", "", $token);
    $token = str_replace("}", "", $token);
    $token = str_replace('"', "!|xm|>", $token);
    return $token;
  }

  private function getRestTokenPegawai($objectPegawai) {
    $callback = [
      'id' => $objectPegawai['id'],
      'username' => $objectPegawai['nip'],
      'password' => $objectPegawai['password']
    ];
    $token = $this->encrypt('sidak.bkpsdmsitubondokab', $callback);
    $token = str_replace("{", "", $token);
    $token = str_replace("}", "", $token);
    $token = str_replace('"', "!|xm|>", $token);
    return $token;
  }

  private function reformToken($token) {
    $token = $token;
    $token = "{".$token."}";
    $token = str_replace("!|xm|>", '"', $token);
    return $token;
  }

  public function createRestUser(Request $request) {
    $pswd = password_hash($this->generatePassword(), PASSWORD_DEFAULT);
    DB::table('m_admin')->insert([
      'id' => NULL,
      'username' => $request->username,
      'password' => $pswd,
      'unitOrganisasi' => 431,
      'idAppRoleUser' => 1,
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $callback = [
      'status' => 2,
      'message' => 'Rest User berhasil dibuat.'
    ];
    return $callback;
  }

  public function generatePassword() {
    $letters = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z']; // 0
    $numbers = [0,1,2,3,4,5,6,7,8,9]; // 1
    $specials = ['!','@','#','$','%','^','&','*','-','_','+','=']; // 2
    $pswd = '';
    do {
      $random = rand(0,2);
      if ($random === 0) {
        $pswd = $pswd.$letters[rand(0,count($letters)-1)];
      } else if ($random === 1) {
        $pswd = $pswd.$numbers[rand(0,count($numbers)-1)];
      } else {
        $pswd = $pswd.$specials[rand(0,count($specials)-1)];
      }
    } while (strlen($pswd) < 8);
    return $pswd;
  }

  protected function isRestAuth($token) {
    if ($token === null) return [
      'status' => false,
      'message' => 'Forbidden'
    ];
    $tkn = $this->reformToken($token);
    $message = $this->decrypt('sidak.bkpsdmsitubondokab', $tkn);
    if ($message === null) return [
      'status' => false,
      'message' => 'Forbidden'
    ];
    $message = json_decode(DB::table('m_admin')->where([
      ['username', '=', $message['username']],
      ['password', '=', $message['password']]
    ])->get(), true);
    return [
      'status' => count($message) > 0,
      'message' => count($message) > 0 ? 'Authenticated' : 'Forbidden'
    ];
  }

  public function restLogin(Request $request) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $usr = $request->username;
    $pwd = $request->password;
    $users = json_decode(DB::table('m_pegawai')->where([
      ['nip', '=', $usr],
    ])->get(), true);
    if (count($users) === 0) {
      return [
        'status' => false,
        'message' => 'Username/Password salah!'
      ];
    }

    $userFind = [];
    foreach ($users as $idx => $user) {
      if ($user['nip'] === $usr && password_verify($pwd, $user['password'])) {
        array_push($userFind, $user);
      }
    }
    if (count($userFind) === 1) {
      return [
        'status' => true,
        'message' => $this->getRestTokenPegawai($userFind[0])
      ];
    }

    /// Jika proses pengecekan password salah tapi nip benar
    $response = (new ApiSiasnController)->getAuthToken($usr, $pwd);
    if(!isset($response['access_token'])) {
      return [
        'status' => false,
        'message' => 'Username/password salah!',
      ];
    }
    $newPwd = password_hash($pwd, PASSWORD_DEFAULT);
    DB::table('m_pegawai')->where([
      ['nip', '=', $usr]
    ])->update([
      'password' => $newPwd
    ]);

    $userFind = [];
    foreach ($users as $idx => $user) {
      if ($user['nip'] === $usr && password_verify($pwd, $user['password'])) {
        array_push($userFind, $user);
      }
    }
    if (count($userFind) === 1) {
      return [
        'status' => true,
        'message' => $this->getRestTokenPegawai($userFind[0])
      ];
    }
    return [
      'status' => false,
      'message' => 'Error! Silahkan menghubungi Admin BKPSDM!'
    ];
  }

  public function restGetDocument(Request $request, $namaDokumen=NULL) {
    $authentication = $this->isRestAuth($request->header('Auth'));
    if (!$authentication['status']) {
      return $authentication;
    }
    $dokumen = '';
    if ($namaDokumen !== NULL && str_contains($namaDokumen, 'DOK_')) {
      $folderDokumen = '';
      if (str_contains($namaDokumen, '_JABATAN_')) $folderDokumen = 'jabatan';
      else if (str_contains($namaDokumen, '_PAK_')) $folderDokumen = 'pak';
      else if (str_contains($namaDokumen, '_IJAZAH_')) $folderDokumen = 'pendidikan';
      else if (str_contains($namaDokumen, '_TRANSKRIP_')) $folderDokumen = 'pendidikan';
      else if (str_contains($namaDokumen, '_SKP_')) $folderDokumen = 'skp';
      else if (str_contains($namaDokumen, '_HUKUMAN_DISIPLIN_')) $folderDokumen = 'hukdis';
      else if (str_contains($namaDokumen, '_PANGKAT_')) $folderDokumen = 'pangkat';

      // $dokumen = base64_encode(file_get_contents(storage_path('app/dokumen/'.$folderDokumen.'/'.$namaDokumen.'.pdf')));
      $dokumen = file_get_contents(storage_path('app/dokumen/'.$folderDokumen.'/'.$namaDokumen.".pdf"));
    }
    // return $dokumen;
    return response($dokumen, 200, [
      'Content-Type' => 'application/pdf'
    ]);
  }

  // public function restGetProvideToPangkat(Request $request, $nipBaru, $periode) {
  //   $authentication = $this->isRestAuth($request->header('Auth'));
  //   if (!$authentication['status']) {
  //     return $authentication;
  //   }
  //   $dataSingle = json_decode(DB::table('m_pegawai')->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')->join('m_data_pangkat', 'm_pegawai.id', '=', 'm_data_pangkat.idPegawai')->join('m_daftar_pangkat', 'm_data_pangkat.idDaftarPangkat', '=', 'm_daftar_pangkat.id')->join('m_data_pendidikan', 'm_pegawai.id', '=', 'm_data_pendidikan.idPegawai')->join('m_tingkat_pendidikan', 'm_data_pendidikan.idTingkatPendidikan', '=', 'm_tingkat_pendidikan.id')->join('m_daftar_pendidikan', 'm_data_pendidikan.idDaftarPendidikan', '=', 'm_daftar_pendidikan.id')->whereIn('m_data_pangkat.idUsulanStatus', [3,4])->whereIn('m_data_pendidikan.idUsulanStatus', [3,4])->where([
  //     ['m_pegawai.nip', '=', $nipBaru],
  //     ['m_data_pangkat.tmt', '<=', $periode],
  //     ['m_data_pendidikan.tanggalDokumen', '<=', $periode],
  //     ['m_data_pangkat.idUsulan', '=', 1],
  //     ['m_data_pangkat.idUsulanHasil', '=', 1],
  //     ['m_data_pendidikan.idUsulan', '=', 1],
  //     ['m_data_pendidikan.idUsulanHasil', '=', 1]
  //   ])->orderBy('m_daftar_pangkat.id', 'desc')->orderBy('m_tingkat_pendidikan.id', 'desc')->limit(1)->get([
  //     'm_pegawai.id AS asn_id',
  //     'm_pegawai.nip AS asn_nip',
  //     'm_data_pribadi.tempatLahir AS asn_tempat_lahir',
  //     'm_data_pribadi.tanggalLahir AS asn_tanggal_lahir',
  //     'm_daftar_pangkat.golongan AS asn_golongan',
  //     'm_daftar_pangkat.pangkat AS asn_pangkat',
  //     'm_data_pangkat.tmt AS asn_tmt_golongan',
  //     'm_data_pangkat.masaKerjaTahun AS asn_mk_golongan_tahun',
  //     'm_data_pangkat.masaKerjaBulan AS asn_mk_golongan_bulan',
  //     'm_tingkat_pendidikan.nama AS asn_tingkat_pendidikan',
  //     'm_daftar_pendidikan.nama AS asn_pendidikan'
  //   ]), true);
  //   foreach ($dataSingle as $idx => $dt) {
  //     $jabatans = json_decode(DB::table('m_data_jabatan')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->join('m_jenis_jabatan', 'm_jabatan.idJenisJabatan', '=', 'm_jenis_jabatan.id')->join('m_unit_organisasi AS unor_child', 'm_jabatan.kodeKomponen', '=', 'unor_child.kodeKomponen')->leftJoin('m_unit_organisasi AS unor_parent', 'unor_child.idBknAtasan', '=', 'unor_parent.idBkn')->leftJoin('m_dokumen', 'm_data_jabatan.idDokumen', '=', 'm_dokumen.id')->whereIn('m_data_jabatan.idUsulanStatus', [3,4])->where([
  //       ['m_data_jabatan.idPegawai', '=', $dt['asn_id']],
  //       ['m_data_jabatan.tmt', '<=', $periode],
  //       ['m_data_jabatan.idUsulan', '=', 1],
  //       ['m_data_jabatan.idUsulanHasil', '=', 1]
  //     ])->orderBy('m_data_jabatan.tmt', 'desc')->get([
  //       'm_jenis_jabatan.nama AS jabatan_jenis',
  //       'm_jabatan.nama AS jabatan_nama',
  //       'm_data_jabatan.tmt AS jabatan_tmt',
  //       'unor_child.nama AS jabatan_unor',
  //       'unor_parent.nama AS jabatan_unor_induk',
  //       'm_data_jabatan.tanggalDokumen AS jabatan_dokumen_tanggal',
  //       'm_data_jabatan.nomorDokumen AS jabatan_dokumen_nomor',
  //       'm_dokumen.nama AS jabatan_dokumen_url'
  //     ]), true);
  //     for($i = 0; $i < count($jabatans); $i++) {
  //       if($jabatans[$i]['jabatan_dokumen_url'] !== null) {
  //         $jabatans[$i]['jabatan_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$jabatans[$i]['jabatan_dokumen_url'];
  //       }
  //     }
  //     $dataSingle[$idx]['asn_jabatans'] = $jabatans;
  //     $angkaKredits = json_decode(DB::table('m_data_angka_kredit')->leftJoin('m_daftar_jenis_angka_kredit', 'm_data_angka_kredit.idDaftarJenisAngkaKredit', '=', 'm_daftar_jenis_angka_kredit.id')->join('m_data_jabatan', 'm_data_angka_kredit.idDataJabatan', '=', 'm_data_jabatan.id')->join('m_jabatan', 'm_data_jabatan.idJabatan', '=', 'm_jabatan.id')->leftJoin('m_dokumen', 'm_data_angka_kredit.idDokumen', '=', 'm_dokumen.id')->whereIn('m_data_angka_kredit.idUsulanStatus', [3,4])->where([
  //       ['m_data_angka_kredit.idPegawai', '=', $dt['asn_id']],
  //       ['m_data_jabatan.idUsulan', '=', 1],
  //       ['m_data_jabatan.idUsulanHasil', '=', 1]
  //     ])->get([
  //       'm_daftar_jenis_angka_kredit.jenisAngkaKredit AS kredit_jenis',
  //       'm_jabatan.nama AS kredit_jabatan',
  //       'm_data_angka_kredit.tahun AS kredit_tahun',
  //       'm_data_angka_kredit.periodePenilaianMulai AS kredit_periode_mulai',
  //       'm_data_angka_kredit.periodePenilaianSelesai AS kredit_periode_selesai',
  //       'm_data_angka_kredit.angkaKreditUtama AS kredit_nilai_ak_utama',
  //       'm_data_angka_kredit.angkaKreditPenunjang AS kredit_nilai_ak_penunjang',
  //       'm_data_angka_kredit.angkaKreditTotal AS kredit_nilai_ak_total',
  //       'm_data_angka_kredit.tanggalDokumen AS kredit_dokumen_tanggal',
  //       'm_data_angka_kredit.nomorDokumen AS kredit_dokumen_nomor',
  //       'm_dokumen.nama AS kredit_dokumen_url'
  //     ]), true);
  //     for($i = 0; $i < count($angkaKredits); $i++) {
  //       if($angkaKredits[$i]['kredit_dokumen_url'] !== null) {
  //         $angkaKredits[$i]['kredit_dokumen_url'] = 'https://sidak.situbondokab.go.id/api/rest/get/dokumen/'.$angkaKredits[$i]['kredit_dokumen_url'];
  //       }
  //     }
  //     $dataSingle[$idx]['asn_kredits'] = $angkaKredits;
  //   }

  //   return $dataSingle;

  //   ///// IKI BERLAKU SEARCH 1 ORANG
  //   // SELECT
  //   //   m_pegawai.nip,
  //   //   m_data_pribadi.nama,
  //   //   m_data_pribadi.tempatLahir,
  //   //   m_data_pribadi.tanggalLahir,
  //   //   m_daftar_pangkat.golongan,
  //   //   m_daftar_pangkat.pangkat,
  //   //   m_tingkat_pendidikan.nama AS 'tingkat_pendidikan',
  //   //   m_daftar_pendidikan.nama AS 'pendidikan'
  //   // FROM
  //   //   m_pegawai
  //   //   INNER JOIN m_data_pribadi ON m_pegawai.id = m_data_pribadi.idPegawai
  //   //   INNER JOIN m_data_pangkat ON m_pegawai.id = m_data_pangkat.idPegawai
  //   //   INNER JOIN m_daftar_pangkat ON m_data_pangkat.idDaftarPangkat = m_daftar_pangkat.id
  //   //   INNER JOIN m_data_pendidikan ON m_pegawai.id = m_data_pendidikan.idPegawai
  //   //   INNER JOIN m_tingkat_pendidikan ON m_data_pendidikan.idTingkatPendidikan = m_tingkat_pendidikan.id
  //   //   INNER JOIN m_daftar_pendidikan ON m_data_pendidikan.idDaftarPendidikan = m_daftar_pendidikan.id
  //   // WHERE
  //   //   m_pegawai.nip = '196910142008011011'
  //   // ORDER BY
  //   //   m_daftar_pangkat.id DESC,
  //   //   m_tingkat_pendidikan.id DESC
  //   // LIMIT 1;
  // }
}
