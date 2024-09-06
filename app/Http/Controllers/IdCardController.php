<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdCardController extends Controller
{
	private function getImageBlob($namaFolder,$namaFileDenganEkstensi) {
		$path = storage_path("app/".$namaFolder."/".$namaFileDenganEkstensi);
    try {
      $mimeType = mime_content_type($path);
      $getImageFromServerFolder = base64_encode(file_get_contents($path));
      // $blob = 'data:'.$mimeType.';base64,'.$getImageFromServerFolder;
			$blob = $getImageFromServerFolder;
    } catch (Exception $ex) {
      $blob = '';
    }
		return $blob;
	}
	public function getIdCard() {
		$bgDepan = $this->getImageBlob('idcard/background', 'background-depan.jpeg');
		$bgBelakang = $this->getImageBlob('idcard/background', 'background-belakang.jpeg');
		$logo = $this->getImageBlob('idcard/component', 'logo-situbondo.png');
		$line = $this->getImageBlob('idcard/component', 'line.jpeg');
    $foto = $this->getImageBlob('foto', '199601162019031001.jpg');

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
				'data' => [
          ['foto' => $foto]
        ]
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
      for ($j = 0; $j < count($allUnors); $j++) {
        if ($data[$i]['kodeKomponen'] === $allUnors[$j]['kodeKomponen']) {
          $data[$i]['unitOrganisasi'] = $allUnors[$j]['nama'];
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
