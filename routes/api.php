<?php

use App\Http\Controllers\DataCpnsPnsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::delete('unit-organisasi/{id}', 'JabatanUnitOrganisasiController@deleteUnitOrganisasi');
Route::delete('uang-kinerja/{id}', 'JabatanUnitOrganisasiController@deleteUangKinerja');
Route::delete('kelas-jabatan/{id}', 'JabatanUnitOrganisasiController@deleteKelasJabatan');
Route::delete('jabatan/{id}', 'JabatanUnitOrganisasiController@deleteJabatan');

Route::post('auth', 'Controller@isAuthorized');
Route::post('login', 'LoginController@login');
Route::post('forget-password', 'LoginController@fogetPassword');
Route::post('data-pasangan', 'DataPasanganController@insertDataPasangan');
Route::post('data-pasangan/{id}', 'DataPasanganController@insertDataPasangan'); // for insert to update
Route::post('data-anak', 'DataAnakController@insertDataAnak');
Route::post('data-anak/{id}', 'DataAnakController@insertDataAnak'); // for insert to update
Route::post('data-pendidikan', 'DataPendidikanController@insertDataPendidikan');
Route::post('data-pendidikan/{id}', 'DataPendidikanController@insertDataPendidikan'); // for insert to update
Route::post('data-golpang', 'DataGolonganPangkatController@insertDataGolPang');
Route::post('data-golpang/{id}', 'DataGolonganPangkatController@insertDataGolPang'); // for insert to update
Route::post('data-diklat', 'DataDiklatController@insertDataDiklat');
Route::post('data-diklat/{id}', 'DataDiklatController@insertDataDiklat'); // for insert to update
Route::post('data-hukdis', 'DataHukumanDisiplinController@insertDataHukdis');
Route::post('data-hukdis/{id}', 'DataHukumanDisiplinController@insertDataHukdis'); // for insert to update
Route::post('data-jabatan', 'DataJabatanController@insertDataJabatan');
Route::post('data-jabatan/{id}', 'DataJabatanController@insertDataJabatan'); // for insert to update
Route::post('data-skp', 'DataSkpController@insertDataSkp');
Route::post('data-skp/{id}', 'DataSkpController@insertDataSkp'); // for insert to update
ROute::post('unit-organisasi', 'JabatanUnitOrganisasiController@insertUnitOrganisasi');
Route::post('uang-kinerja', 'JabatanUnitOrganisasiController@insertUangKinerja');
Route::post('kelas-jabatan', 'JabatanUnitOrganisasiController@insertKelasJabatan');
Route::post('jabatan', 'JabatanUnitOrganisasiController@insertJabatan');
Route::post('data-status-kepegawaian', 'DataStatusKepegawaianController@insertDataStatusKepegawaian');
Route::post('data-atasan', 'DataAtasanController@insertDataAtasan');
Route::post('user-pegawai', 'DataPegawaiController@insertDataPegawai');
Route::post('data-penghargaan', 'DataPenghargaanController@insertDataPenghargaan');
Route::post('data-penghargaan/{id}', 'DataPenghargaanController@insertDataPenghargaan');

Route::put('data-pribadi/{id}', 'DataPribadiController@updateDataPribadi');
Route::put('data-cpns-pns/{id}', 'DataCpnsPnsController@updateDataCpnsPns');
Route::put('unit-organisasi/{id}', 'JabatanUnitOrganisasiController@updateUnitOrganisasi');
Route::put('uang-kinerja/{id}', 'JabatanUnitOrganisasiController@updateUangKinerja');
Route::put('kelas-jabatan/{id}', 'JabatanUnitOrganisasiController@updateKelasJabatan');
Route::put('jabatan-detail/{id}', 'JabatanUnitOrganisasiController@updateJabatan');
Route::put('usulan/{id}', 'UsulanController@updateUsulan');
Route::put('change-password', 'LoginController@changePassword');

Route::get('main-menu', 'MenuController@getMainMenu');
Route::get('pegawai-menu', 'MenuController@getPegawaiMenu');
Route::get('dokumen-kategori/{keterangan}', 'DokumenController@getDokumenKategori');
Route::get('data-pribadi/{idPegawai}', 'DataPribadiController@getDataPribadi');
Route::get('data-short-brief/{idPegawai}', 'DataShortBriefController@getDataShortBrief');
Route::get('data-cpns-pns/{idPegawai}', 'DataCpnsPnsController@getDataCpnsPns');
Route::get('data-pasangan/{idPegawai}', 'DataPasanganController@getDataPasangan');
Route::get('data-pasangan/{idPegawai}/{id}', 'DataPasanganController@getDataPasangan');
Route::get('status-perkawinan', 'DataPasanganController@getStatusPerkawinan');
Route::get('data-anak/{idPegawai}', 'DataAnakController@getDataAnak');
Route::get('data-anak/{idPegawai}/{id}', 'DataAnakController@getDataAnak');
Route::get('status-anak', 'DataAnakController@getStatusAnak');
Route::get('orang-tua/{idPegawai}', 'DataAnakController@getDataOrangTua');
Route::get('jenis-pendidikan', 'DataPendidikanController@getJenisPendidikan');
Route::get('tingkat-pendidikan', 'DataPendidikanController@getTingkatPendidikan');
Route::get('data-pendidikan/{idPegawai}', 'DataPendidikanController@getDataPendidikan');
Route::get('data-pendidikan/{idPegawai}/{id}', 'DataPendidikanController@getDataPendidikan');
Route::get('unit-organisasi', 'JabatanUnitOrganisasiController@getUnitOrganisasi');
Route::get('unit-organisasi/{kodeKomponen}', 'JabatanUnitOrganisasiController@getUnitOrganisasi');
Route::get('filter-opd', 'JabatanUnitOrganisasiController@getFilterOpd');
Route::get('uang-kinerja', 'JabatanUnitOrganisasiController@getUangKinerja');
Route::get('kelas-jabatan', 'JabatanUnitOrganisasiController@getKelasJabatan');
Route::get('jabatan', 'JabatanUnitOrganisasiController@getJabatan');
Route::get('jabatan/{kodeKomponen}', 'JabatanUnitOrganisasiController@getJabatan');
Route::get('jabatan-detail/{idJabatan}', 'JabatanUnitOrganisasiController@getJabatanDetail');
Route::get('jabatan-all-group/{kodeKomponen}', 'JabatanUnitOrganisasiController@getJabatanAllGroup');
Route::get('tugas-tambahan', 'JabatanUnitOrganisasiController@getTugasTambahan');
Route::get('jenis-golpang', 'DataGolonganPangkatController@getJenisGolPang');
Route::get('daftar-golpang', 'DataGolonganPangkatController@getDaftarGolPang');
Route::get('data-golpang/{idPegawai}', 'DataGolonganPangkatController@getDataGolPang');
Route::get('data-golpang/{idPegawai}/{id}', 'DataGolonganPangkatController@getDataGolPang');
Route::get('data-skp/{idPegawai}', 'DataSkpController@getDataSkp');
Route::get('data-skp/{idPegawai}/{id}', 'DataSkpController@getDataSkp');
Route::get('jenis-jabatan', 'DataSkpController@getJenisJabatan');
Route::get('jenis-peraturan-kinerja', 'DataSkpController@getJenisPeraturanKinerja');
Route::get('status-pejabat-atasan-penilai', 'DataSkpController@getJenisStatusPejabatAtasanPenilai');
Route::get('jenis-diklat', 'DataDiklatController@getJenisDiklat');
Route::get('daftar-diklat', 'DataDiklatController@getDaftarDiklat');
Route::get('daftar-instansi-diklat', 'DataDiklatController@getDaftarInstansiDiklat');
Route::get('data-diklat/{idPegawai}', 'DataDiklatController@getDataDiklat');
Route::get('data-diklat/{idPegawai}/{id}', 'DataDiklatController@getDataDiklat');
Route::get('jenis-hukdis', 'DataHukumanDisiplinController@getJenisHukdis');
Route::get('daftar-hukdis', 'DataHukumanDisiplinController@getDaftarHukdis');
Route::get('daftar-hukdis/{idJenisHukdis}', 'DataHukumanDisiplinController@getDaftarHukdis');
Route::get('dasar-hukum-hukdis', 'DataHukumanDisiplinController@getDasarHukumHukdis');
Route::get('alasan-hukdis', 'DataHukumanDisiplinController@getDaftarAlasanHukdis');
Route::get('data-hukdis/{idPegawai}', 'DataHukumanDisiplinController@getDataHukdis');
Route::get('data-hukdis/{idPegawai}/{id}', 'DataHukumanDisiplinController@getDataHukdis');
Route::get('data-jabatan/{idPegawai}', 'DataJabatanController@getDataJabatan');
Route::get('data-jabatan/{idPegawai}/{id}', 'DataJabatanController@getDataJabatan');
Route::get('usulan/{idUsulanStatus}', 'UsulanController@getUsulan');
Route::get('usulan/{idUsulanStatus}/{idPegawai}', 'UsulanController@getUsulan');
Route::get('usulan-detail/{idUsulan}/{usulanKriteria}', 'UsulanController@getUsulanDetail');
Route::get('has-sub-organisasi/{kodeKomponen}', 'JabatanUnitOrganisasiController@getHasSubOrganisasi');
Route::get('pendidikan/{idTingkatPendidikan}', 'DataPendidikanController@getDaftarPendidikan');
Route::get('daftar-status-kepegawaian', 'DataStatusKepegawaianController@getDaftarStatusKepegawaian');
Route::get('data-status-kepegawaian/{idPegawai}', 'DataStatusKepegawaianController@getDataStatusKepegawaian');
Route::get('daftar-atasan', 'DataAtasanController@getDaftarAtasan');
Route::get('data-atasan/{idPegawai}', 'DataAtasanController@getDataAtasan');
Route::get('data-bawahan/{idAtasan}', 'DataAtasanController@getDataBawahan');
Route::get('data-penghargaan/{idPegawai}', 'DataPenghargaanController@getDataPenghargaan');
Route::get('data-penghargaan/{idPegawai}/{idUsulan}', 'DataPenghargaanController@getDataPenghargaan');
Route::get('jenis-penghargaan', 'DataPenghargaanController@getDaftarJenisPenghargaan');

Route::get('list-pegawai', 'ListPegawaiController@getListPegawai');
Route::get('total-pegawai', 'ListPegawaiController@getTotalPegawai');
Route::get('nama-unit-organisasi', 'ListPegawaiController@getNamUnitOrganisasi');

//Users Account
Route::get('user-asn', 'UsersController@getAllUserPegawai');
Route::get('user-admin', 'UsersController@getAllUserAdmin');
Route::get('user-role', 'UsersController@getAllUserRole');
Route::post('reset-password', 'UsersController@resetPassword');
Route::post('user-admin', 'UsersController@insertUserAdmin');

// Dashboard
Route::get('pegawai-ultah/{numberofMonth}', 'DashboardController@getPegawaiUltah');

// API SIASN SYNC
Route::get('siasn/jabatan/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncJabatanASN');
Route::get('siasn/jabatan/riwayat/sync/all/{from}/{to}/{timeForNoCache}', 'ApiSiasnSyncController@syncJabatanASNAll');
Route::get('siasn/diklat/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncDiklatASN');
Route::get('siasn/pangkat-golongan/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncPangkatGolonganASN');
Route::get('siasn/pendidikan/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncPendidikanASN');
Route::get('siasn/hukuman-disiplin/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncHukdisASN');
Route::get('siasn/penghargaan/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncPenghargaanASN');

// API SIASN MASTER
/// DOKUMEN
Route::get('siasn/dokumen/riwayat', 'ApiSiasnController@getDokumenRiwayat');
/// DIKLAT dan KURSUS
Route::get('siasn/diklat/riwayat/detail/{idRiwayatDiklat}', 'ApiSiasnController@getRiwayatDiklatASNDetail');
Route::get('siasn/diklat/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatDiklatASN');
Route::get('siasn/kursus/riwayat/detail/{idRiwayatKursus}', 'ApiSiasnController@getRiwayatKursusASNDetail');
Route::get('siasn/kursus/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatKursusASN');
/// JABATAN
Route::get('siasn/jabatan/riwayat/detail/{idRiwayatJabatan}', 'ApiSiasnController@getRiwayatJabatanASNDetail');
Route::get('siasn/jabatan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatJabatanASN');
/// GOLONGAN
Route::get('siasn/golongan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatPangkatGolonganASN');
/// PENDIDIKAN
Route::get('siasn/pendidikan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatPendidikanASN');
/// HUKDIS
Route::get('siasn/hukdis/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatHukdisASN');
/// ANGKA KREDIT
Route::get('siasn/angka-kredit/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatAngkaKreditASN');
/// DATA UTAMA
Route::get('siasn/data-utama/{nipBaru}', 'ApiSiasnController@getDataUtamaASN');
/// DATA PENGHARGAAN
Route::get('siasn/penghargaan/{nipBaru}', 'ApiSiasnController@getRiwayatPenghargaanASN');