<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdCardController extends Controller
{
	private function getImageBlob($namaFolder,$namaFoto) {
    $blob = '';
    $extension = '';
    $listExtensi = ['jpg', 'jpeg', 'png'];
    foreach ($listExtensi as $ekstensi) {
      try {
        $path = storage_path("app/".$namaFolder."/".$namaFoto.".".$ekstensi);
        $mimeType = mime_content_type($path);
        $getImageFromServerFolder = base64_encode(file_get_contents($path));
        // $blob = 'data:'.$mimeType.';base64,'.$getImageFromServerFolder;
        $blob = $getImageFromServerFolder;
        $extension = $ekstensi;
      } catch (Exception $ex) {}
    }
		return [
      'blob' => $blob,
      'ekstensi' => $extension
    ];
	}
	public function getIdCard(Request $request) {
    $authenticated = $this->isAuth($request)['authenticated'];
    $username = $this->isAuth($request)['username'];
    if(!$authenticated) return $this->encrypt($username, json_encode([
      'message' => $authenticated == true ? 'Authorized' : 'Not Authorized',
      'status' => $authenticated === true ? 1 : 0
    ]));
    $message = json_decode($this->decrypt($username, $request->message), true);
    $listFoto = [];
    foreach ($message['listNip'] as $nip) {
      $foto = $this->getImageBlob('foto', $nip);
      array_push($listFoto, [$nip => [
        'foto' => $foto['blob'],
        'ekstensi' => $foto['ekstensi']
        ]]);
    }
		$bgDepan = $this->getImageBlob('idcard/background', 'background-depan')['blob'];
		$bgBelakang = $this->getImageBlob('idcard/background', 'background-belakang')['blob'];
		$logo = $this->getImageBlob('idcard/component', 'logo-situbondo')['blob'];
		$line = $this->getImageBlob('idcard/component', 'line')['blob'];


		return [
			'message' => [
        'template' => [
          'background' => [
            'depan' => $bgDepan,
            'belakang' => $bgBelakang
          ],
          'components' => [
            'line' => $line,
            'logo' => $logo,
            'ttd' => '',
            'stempel' => ''
          ],
        ],
				'foto' => json_encode($listFoto)
			],
			'status' => 2
		];
	}
  public function getListPegawai() {
    $data = json_decode(DB::table('m_pegawai')
    ->join('m_data_pribadi', 'm_pegawai.id', '=', 'm_data_pribadi.idPegawai')
    ->join('m_data_status_kepegawaian', 'm_pegawai.id', '=', 'm_data_status_kepegawaian.idPegawai')
    ->join('v_m_jabatan_group', 'm_pegawai.id', '=', 'v_m_jabatan_group.idPegawai')
    ->leftJoin('v_m_pendidikan_group', 'm_pegawai.id', '=', 'v_m_pendidikan_group.idPegawai')
    ->whereNotIn('m_data_status_kepegawaian.idDaftarStatusKepegawaian', [8,9,10,11,12,13,14])
    ->selectRaw(
      "CONCAT(IF((gelarDepan IS NULL OR gelarDepan=''), '', CONCAT(gelarDepan,' ')), nama, IF((gelarBelakang IS NULL OR gelarBelakang=''), '', CONCAT(', ', gelarBelakang))) AS nama, m_pegawai.id as id,
      m_pegawai.nip as nip,
      v_m_jabatan_group.jabatan as jabatan,
      v_m_jabatan_group.kodeKomponen as kodeKomponen"
    )
    ->get(), true);

    $listKodeKomponen = [];
    foreach ($data as $value) {
      $kdExplode = explode(".", $value['kodeKomponen']);
      $countKdExplode = count($kdExplode);
      for ($i = 0; $i < $countKdExplode; $i++) {
        if (count($kdExplode) === 0) break;
        $kdImplode = implode(".", $kdExplode);
        $isHasKd = false;
        foreach ($listKodeKomponen as $listKd) {
          if ($kdImplode === $listKd) $isHasKd = true;
        }
        if (!$isHasKd) {
          $listKodeKomponen[] = $kdImplode;
        }
        array_pop($kdExplode);
      }
    }
    $allUnors = (new DataJabatanController)->getAllUnor(DB::table('m_unit_organisasi')->whereIn('kodeKomponen', $listKodeKomponen)->where([
      ['idBkn', '!=', ''],
      ['kodeKomponen', 'NOT LIKE', '-%']
    ])->get());
    for ($i = 0; $i < count($data); $i++) {
      if (str_contains($data[$i]['kodeKomponen'], "-")) {
        $data[$i]['unitOrganisasi'] = "(Unit organisasi tidak ada di dalam database. Silahkan update atau konsultasi dengan BKPSDM.)";
        continue;
      }
      $data[$i]['hasPhoto'] = $this->getImageBlob('foto', $data[$i]['nip'])['blob'] !== '';
      for ($j = 0; $j < count($allUnors); $j++) {
        if ($data[$i]['kodeKomponen'] === $allUnors[$j]['kodeKomponen']) {
          $unorTemp = explode(' pada ', $allUnors[$j]['nama']);
          $data[$i]['unitOrganisasi'] = $unorTemp[count($unorTemp)-1];
          break;
        }
      }
    }

    $callback = [
      'message' => [
        'pegawai' => $data,
      ],
      'status' => 2
    ];
    return $callback;
  }
}
