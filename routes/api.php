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
Route::post('user-pegawai', 'UsersController@insertDataPegawai');

Route::put('data-pribadi/{id}', 'DataPribadiController@updateDataPribadi');
Route::put('data-cpns-pns/{id}', 'DataCpnsPnsController@updateDataCpnsPns');
Route::put('unit-organisasi/{id}', 'JabatanUnitOrganisasiController@updateUnitOrganisasi');
Route::put('uang-kinerja/{id}', 'JabatanUnitOrganisasiController@updateUangKinerja');
Route::put('kelas-jabatan/{id}', 'JabatanUnitOrganisasiController@updateKelasJabatan');
Route::put('jabatan/{id}', 'JabatanUnitOrganisasiController@updateJabatan');
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

Route::get('list-pegawai', 'ListPegawaiController@getListPegawai');
Route::get('total-pegawai', 'ListPegawaiController@getTotalPegawai');
Route::get('nama-unit-organisasi', 'ListPegawaiController@getNamUnitOrganisasi');

// Dashboard
Route::get('pegawai-ultah/{numberofMonth}', 'DashboardController@getPegawaiUltah');

// Route::get('coba/{date}', 'Controller@getPegawaiByDate');