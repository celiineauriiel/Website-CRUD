<?php
// File: tests/SiswaCrudTest.php (revisi)

namespace Tests;

// Memuat file function.php agar fungsi dan variabel globalnya bisa diakses
require_once __DIR__ . '/../function.php';

class SiswaCrudTest extends DatabaseTestCase
{
    private array $dataSiswaValid;

    protected function setUp(): void
    {
        parent::setUp();
        
        // PERBAIKAN: Menggunakan data yang valid sesuai dengan logika baru.
        // NIS '5026...' sekarang harus berpasangan dengan jurusan 'Sistem Informasi'.
        $this->dataSiswaValid = [
            'nis' => '5026221001', 
            'nama' => 'Budi Santoso', 
            'tmpt_Lahir' => 'Surabaya',
            'tgl_Lahir' => '2004-05-10', 
            'jekel' => 'Laki - Laki', 
            'jurusan' => 'Sistem Informasi', // Diperbarui
            'ipk' => 3.75, 
            'jalur_masuk' => 'SNBT',
            'email' => 'budi.santoso@example.com', 
            'alamat' => 'Jl. Kenjeran No. 123'
        ];
        $_FILES = []; // Reset file upload
    }
    
    /** @test */
    public function bisa_menambah_dan_mengambil_data_siswa(): void
    {
        // PERBAIKAN: Memastikan variabel global tersedia dalam lingkup tes
        global $jurusan_nis_prefixes;
        
        $hasilTambah = tambah($this->dataSiswaValid);
        
        $this->assertEquals(1, $hasilTambah, "Fungsi tambah() seharusnya berhasil dan mengembalikan 1.");

        $dataDariDB = query("SELECT * FROM siswa WHERE nis = '5026221001'");
        $this->assertCount(1, $dataDariDB);
        $this->assertEquals('Budi Santoso', $dataDariDB[0]['nama']);
    }

    /** @test */
    public function bisa_mengubah_data_siswa(): void
    {
        // PERBAIKAN: Memastikan variabel global tersedia dalam lingkup tes
        global $jurusan_nis_prefixes;

        // Tambah data awal
        tambah($this->dataSiswaValid);
        
        // Siapkan data untuk diubah
        $dataUbah = $this->dataSiswaValid;
        $dataUbah['nama'] = "Budi Hartono";
        $dataUbah['gambarLama'] = '';
        
        $hasilUbah = ubah($dataUbah);
        
        $this->assertEquals(1, $hasilUbah, "Fungsi ubah() seharusnya berhasil dan mengembalikan 1.");
        
        $dataTerbaru = query("SELECT nama FROM siswa WHERE nis = '5026221001'")[0];
        $this->assertEquals("Budi Hartono", $dataTerbaru['nama']);
    }

    /** @test */
    public function bisa_menghapus_data_siswa(): void
    {
        // PERBAIKAN: Memastikan variabel global tersedia dalam lingkup tes
        global $jurusan_nis_prefixes;

        // Tambah data awal
        tambah($this->dataSiswaValid);

        // Hapus data
        $hasilHapus = hapus($this->dataSiswaValid['nis']);
        $this->assertEquals(1, $hasilHapus, "Fungsi hapus() seharusnya berhasil dan mengembalikan 1.");

        // Verifikasi data sudah tidak ada
        $dataSetelahHapus = query("SELECT * FROM siswa WHERE nis = '5026221001'");
        $this->assertCount(0, $dataSetelahHapus);
    }
}