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
ROute::post('unit-organisasi', 'JabatanUnitOrganisasiController@insertUnitOrganisasi');
Route::post('uang-kinerja', 'JabatanUnitOrganisasiController@insertUangKinerja');
Route::post('kelas-jabatan', 'JabatanUnitOrganisasiController@insertKelasJabatan');
Route::post('jabatan', 'JabatanUnitOrganisasiController@insertJabatan');
Route::post('data-status-kepegawaian', 'DataStatusKepegawaianController@insertDataStatusKepegawaian');
Route::post('data-atasan', 'DataAtasanController@insertDataAtasan');
Route::post('user-pegawai', 'DataPegawaiController@insertDataPegawai');
Route::post('data-penghargaan', 'DataPenghargaanController@insertDataPenghargaan');
Route::post('data-penghargaan/{id}', 'DataPenghargaanController@insertDataPenghargaan');
Route::post('angka-kredit', 'DataAngkaKreditController@insertDataAngkaKredit');
Route::post('angka-kredit/{id}', 'DataAngkaKreditController@insertDataAngkaKredit');
Route::post('dokumen-elektronik', 'DataDokumenElektronikController@insertDataDokumenElektronik');

Route::put('data-pribadi/{id}', 'DataPribadiController@updateDataPribadi');
Route::put('data-cpns-pns/{id}', 'DataCpnsPnsController@updateDataCpnsPns');
Route::put('unit-organisasi/{id}', 'JabatanUnitOrganisasiController@updateUnitOrganisasi');
Route::put('uang-kinerja/{id}', 'JabatanUnitOrganisasiController@updateUangKinerja');
Route::put('kelas-jabatan/{id}', 'JabatanUnitOrganisasiController@updateKelasJabatan');
Route::put('jabatan-detail/{id}', 'JabatanUnitOrganisasiController@updateJabatan');
Route::put('usulan/{id}', 'UsulanController@updateUsulan');
Route::put('usulan-multiple', 'UsulanController@updateUsulanMultiple');
Route::put('change-password', 'LoginController@changePassword');
Route::put('dokumen-elektronik', 'DataDokumenElektronikController@updateDataDokumenElektronik');
Route::put('data-skp/{tahun}/{idDataSkp}', 'DataSkpController@updateDokumenSkp');

Route::get('main-menu', 'MenuController@getMainMenu');
Route::get('pegawai-menu', 'MenuController@getPegawaiMenu');
Route::get('dokumen-kategori/{keterangan}', 'DokumenController@getDokumenKategori');
Route::get('data-pribadi/{idPegawai}', 'DataPribadiController@getDataPribadi');
Route::get('data-short-brief/{idPegawai}', 'DataShortBriefController@getDataShortBrief');
Route::get('data-cpns-pns/{idPegawai}', 'DataCpnsPnsController@getDataCpnsPns');
Route::get('data-pasangan/created/{idPegawai}', 'DataPasanganController@getDataPasanganCreated');
Route::get('data-pasangan/detail/{idPegawai}/{id}', 'DataPasanganController@getDataPasanganDetail');
Route::get('data-pasangan/{idPegawai}', 'DataPasanganController@getDataPasangan');
Route::get('status-perkawinan', 'DataPasanganController@getStatusPerkawinan');
Route::get('data-anak/created/{idPegawai}', 'DataAnakController@getDataAnakCreated');
Route::get('data-anak/detail/{idPegawai}/{id}', 'DataAnakController@getDataAnakDetail');
Route::get('data-anak/{idPegawai}', 'DataAnakController@getDataAnak');
Route::get('status-anak', 'DataAnakController@getStatusAnak');
Route::get('orang-tua/{idPegawai}', 'DataAnakController@getDataOrangTua');
Route::get('data-pendidikan/created', 'DataPendidikanController@getDataPendidikanCreated');
Route::get('data-pendidikan/{idPegawai}', 'DataPendidikanController@getDataPendidikan');
Route::get('data-pendidikan/{idPegawai}/{id}', 'DataPendidikanController@getDataPendidikanDetail');
Route::get('unit-organisasi', 'JabatanUnitOrganisasiController@getUnitOrganisasi');
Route::get('unit-organisasi/{kodeKomponen}', 'JabatanUnitOrganisasiController@getUnitOrganisasi');
Route::get('filter-opd', 'JabatanUnitOrganisasiController@getFilterOpd');
Route::get('uang-kinerja', 'JabatanUnitOrganisasiController@getUangKinerja');
Route::get('kelas-jabatan', 'JabatanUnitOrganisasiController@getKelasJabatan');
Route::get('jabatan', 'JabatanUnitOrganisasiController@getJabatan');
Route::get('jabatan/{kodeKomponen}', 'JabatanUnitOrganisasiController@getJabatan');
Route::get('jabatan-detail/{idJabatan}', 'JabatanUnitOrganisasiController@getJabatanDetail');
Route::get('data-golpang/created', 'DataGolonganPangkatController@getDataGolPangCreated');
Route::get('data-golpang/{idPegawai}', 'DataGolonganPangkatController@getDataGolPang');
Route::get('data-golpang/detail/{idPegawai}/{id}', 'DataGolonganPangkatController@getDataGolPangDetail');
Route::get('data-skp/{idPegawai}', 'DataSkpController@getDataSkp');
Route::get('data-skp/detail/{idPegawai}/{tahun}/{id}', 'DataSkpController@getDataSkpDetail');
Route::get('jenis-jabatan', 'DataSkpController@getJenisJabatan');
Route::get('jenis-peraturan-kinerja', 'DataSkpController@getJenisPeraturanKinerja');
Route::get('status-pejabat-atasan-penilai', 'DataSkpController@getJenisStatusPejabatAtasanPenilai');
Route::get('data-diklat/created', 'DataDiklatController@getDataDiklatCreated');
Route::get('data-diklat/{idPegawai}', 'DataDiklatController@getDataDiklat');
Route::get('data-diklat/detail/{idPegawai}/{id}', 'DataDiklatController@getDataDiklatDetail');
Route::get('jenis-hukdis', 'DataHukumanDisiplinController@getJenisHukdis');
Route::get('daftar-hukdis', 'DataHukumanDisiplinController@getDaftarHukdis');
Route::get('daftar-hukdis/{idJenisHukdis}', 'DataHukumanDisiplinController@getDaftarHukdis');
Route::get('dasar-hukum-hukdis', 'DataHukumanDisiplinController@getDasarHukumHukdis');
Route::get('alasan-hukdis', 'DataHukumanDisiplinController@getDaftarAlasanHukdis');
Route::get('data-hukdis/{idPegawai}', 'DataHukumanDisiplinController@getDataHukdis');
Route::get('data-hukdis/{idPegawai}/{id}', 'DataHukumanDisiplinController@getDataHukdis');
Route::get('data-jabatan/created', 'DataJabatanController@getDataJabatanCreated');
Route::get('data-jabatan/jabatan/{kodeKomponen}', 'DataJabatanController@getJabatanByKodeKomponen');
Route::get('data-jabatan/detail/{idPegawai}/{id}', 'DataJabatanController@getDataJabatanDetail');
Route::delete('data-jabatan/delete/{id}', 'DataJabatanController@deleteDataJabatan');
Route::get('data-jabatan/{idPegawai}', 'DataJabatanController@getDataJabatan');
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
Route::get('data-penghargaan/created', 'DataPenghargaanController@getDataPenghargaanCreated');
Route::get('data-penghargaan/{idPegawai}', 'DataPenghargaanController@getDataPenghargaan');
Route::get('data-penghargaan/detail/{idPegawai}/{idUsulan}', 'DataPenghargaanController@getDataPenghargaanDetail');
Route::get('angka-kredit/created/{idPegawai}', 'DataAngkaKreditController@getDataCreated');
Route::get('angka-kredit/updated/{idPegawai}/{idUsulan}', 'DataAngkaKreditController@getDataUpdated');
Route::get('angka-kredit/list/{idPegawai}', 'DataAngkaKreditController@getListDataAngkaKredit');
Route::delete('angka-kredit/delete/{id}', 'DataAngkaKreditController@deleteDataJabatan');
Route::get('dokumen-elektronik/list/{idPegawai}', 'DataDokumenElektronikController@getDataCreated');
Route::get('dokumen-elektronik/detail/{idPegawai}/{idDaftarDokumen}', 'DataDokumenElektronikController@getDataDokumenElektronikDetail');

// FOTO
Route::post('change-photo', 'DataShortBriefController@changePhoto');

// GET DOKUMEN
Route::get('dokumen/{idDokumen}', 'DokumenController@getDocument');

Route::get('list-pegawai', 'ListPegawaiController@getListPegawai');

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
Route::get('siasn/angka-kredit/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncAngkaKreditASN');
Route::get('siasn/skp/riwayat/sync/{idPegawai}', 'ApiSiasnSyncController@syncSkpASN');
Route::get('rekap-sinkron/created', 'RekapSinkronController@getRekapSinkron');
Route::post('rekap-sinkron/sync/{idPegawai}', 'RekapSinkronController@onSync');
Route::get('export/created', 'ExportDataController@created');
Route::get('export/data-usulan/created', 'ExportDataController@dataUsulanCreated');
Route::get('idcard', 'ExportDataController@getIdCard');
Route::get('export/{kriteria}', 'ExportDataController@exportData');

// API SIASN MASTER
/// DOKUMEN
// Route::get('siasn/dokumen/riwayat', 'ApiSiasnController@getDokumenRiwayat');
/// DIKLAT dan KURSUS
// Route::get('siasn/diklat/riwayat/detail/{idRiwayatDiklat}', 'ApiSiasnController@getRiwayatDiklatASNDetail');
// Route::get('siasn/diklat/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatDiklatASN');
// Route::get('siasn/kursus/riwayat/detail/{idRiwayatKursus}', 'ApiSiasnController@getRiwayatKursusASNDetail');
// Route::get('siasn/kursus/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatKursusASN');
/// JABATAN
// Route::get('siasn/jabatan/riwayat/detail/{idRiwayatJabatan}', 'ApiSiasnController@getRiwayatJabatanASNDetail');
// Route::get('siasn/jabatan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatJabatanASN');
/// GOLONGAN
// Route::get('siasn/golongan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatPangkatGolonganASN');
/// PENDIDIKAN
// Route::get('siasn/pendidikan/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatPendidikanASN');
/// HUKDIS
// Route::get('siasn/hukdis/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatHukdisASN');
/// ANGKA KREDIT
// Route::get('siasn/angka-kredit/riwayat/{nipBaru}', 'ApiSiasnController@getRiwayatAngkaKreditASN');
/// DATA UTAMA
// Route::get('siasn/data-utama/{nipBaru}', 'ApiSiasnController@getDataUtamaASN');
/// DATA PENGHARGAAN
// Route::get('siasn/penghargaan/{nipBaru}', 'ApiSiasnController@getRiwayatPenghargaanASN');
// Route::post('jajal/{id}', 'ApiSiasnController@insertRiwayatAngkaKreditASN');
// Route::get('acc', 'ApiSiasnController@getAllToken');
// Route::post('jajal-hukdis', 'ApiSiasnController@insertRiwayatHukdisASN');
/// DATA SKP DAN SKP 2022
// Route::get('siasn/get/skp/{nipBaru}', 'ApiSiasnController@getRiwayatSkpASN');



/// REST
// Route::post('create-rest-user', 'RestApiController@createRestUser');
// Route::get('get-rest-token/{username}', 'RestApiController@getRestTokenAdmin');
Route::post('rest/login', 'RestApiController@restLogin');
Route::get('rest/get/dokumen/{namaDokumen}', 'RestApiController@restGetDocument');
Route::get('rest/get/datapangkat/all/{periode}', 'RestApiToAppPangkatPensiunController@restGetAllAsn');
Route::get('rest/get/datapangkat/{nipBaru}/{periode}', 'RestApiToAppPangkatPensiunController@restGet');
Route::get('rest/get/dataslks/{nipBaru}', 'RestApiToAppSlksController@restGet');