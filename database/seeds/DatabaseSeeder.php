<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(AppRoleUserSeeder::class);
    $this->call(AppMainmenuSeeder::class);
    $this->call(AppRoleUserMainMenuSeeder::class);
    $this->call(AppPegawaimenuSeeder::class);
    $this->call(MPegawaiSeeder::class);
    $this->call(MDataPribadiSeeder::class);
    $this->call(AppRoleUserPegawaimenuSeeder::class);
    $this->call(MUsulanSeeder::class);
    $this->call(MUsulanStatusSeeder::class);
    $this->call(MUsulanHasilSeeder::class);
    $this->call(MStatusPerkawinanSeeder::class);
    $this->call(MDokumenSeeder::class);
    $this->call(MDataPasanganSeeder::class);
    $this->call(MDokumenKategoriSeeder::class);
    $this->call(MAdminSeeder::class);
    $this->call(MUnitOrganisasiSeeder::class);
    $this->call(MStatusAnakSeeder::class);
    $this->call(MDataAnakSeeder::class);
    $this->call(MTingkatPendidikanSeeder::class);
    $this->call(MJenisPendidikanSeeder::class);
    $this->call(MDaftarPendidikanSeeder::class);
    $this->call(MDataPendidikanSeeder::class);
    $this->call(MJenisPangkatSeeder::class);
    $this->call(MDaftarPangkatSeeder::class);
    $this->call(MDataPangkatSeeder::class);
    $this->call(MJenisDiklatSeeder::class);
    $this->call(MDaftarDiklatSeeder::class);
    $this->call(MDaftarInstansiDiklatSeeder::class);
    $this->call(MDataDiklatSeeder::class);
    $this->call(MJenisHukumanDisiplinSeeder::class);
    $this->call(MDaftarHukumanDisiplinSeeder::class);
    $this->call(MDaftarDasarHukumHukumanDisiplinSeeder::class);
    $this->call(MDaftarAlasanHukumanDisiplinSeeder::class);
    $this->call(MDataHukumanDisiplinSeeder::class);
    $this->call(MUangKinerjaSeeder::class);
    $this->call(MKelasJabatanSeeder::class);
    $this->call(MJenisJabatanSeeder::class);
    $this->call(MEselonSeeder::class);
    $this->call(MJabatanSeeder::class);
    $this->call(MJabatanTugasTambahanSeeder::class);
    $this->call(MPejabatPengangkatCpnsSeeder::class);
    $this->call(MDataCpnsPnsSeeder::class);
    $this->call(MJenisPeraturanKinerjaSeeder::class);
    $this->call(MStatusPejabatAtasanPenilaiSeeder::class);
    $this->call(MDataAtasanSeeder::class);
    $this->call(MDaftarStatusKepegawaianSeeder::class);
    $this->call(MDataStatusKepegawaianSeeder::class);
    $this->call(MDataJabatanSeeder::class);
  }
}
