<?php
// File: tests/SiswaCrudTest.php (SUDAH DIPERBAIKI)

namespace Tests;

class SiswaCrudTest extends DatabaseTestCase
{
    private $dataSiswaValid;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->dataSiswaValid = [
            'nis' => '5026221001',
            'nama' => 'Budi Santoso',
            'tmpt_Lahir' => 'Surabaya',
            'tgl_Lahir' => '2004-05-10',
            'jekel' => 'Laki - Laki',
            'jurusan' => 'Rekayasa Perangkat Lunak',
            'ipk' => 3.75,
            'jalur_masuk' => 'SNBT',
            'email' => 'budi.santoso@example.com',
            'alamat' => 'Jl. Kenjeran No. 123'
        ];
        
        $_FILES = []; // Pastikan $_FILES kosong untuk semua tes di kelas ini
    }
    
    /**
     * @test
     */
    public function bisaMenambahDataSiswaBaru()
    {
        ob_start();
        $hasil = tambah($this->dataSiswaValid);
        ob_end_clean();
        
        $this->assertEquals(1, $hasil);

        $dataDariDB = query("SELECT * FROM siswa WHERE nis = '5026221001'");
        $this->assertCount(1, $dataDariDB);
        $this->assertEquals('Budi Santoso', $dataDariDB[0]['nama']);
    }

    /**
     * @test
     */
    public function tidakBisaMenambahSiswaDenganNisDuplikat()
    {
        ob_start();
        tambah($this->dataSiswaValid); // Tambahkan siswa pertama kali
        $hasilKedua = tambah($this->dataSiswaValid); // Coba tambahkan lagi
        ob_end_clean();

        $this->assertEquals(0, $hasilKedua, "Fungsi tambah() harus mengembalikan 0 untuk NIS duplikat.");
    }

    /**
     * @test
     */
    public function bisaMengambilSemuaDataSiswa()
    {
        ob_start();
        tambah($this->dataSiswaValid);
        
        $dataKedua = $this->dataSiswaValid;
        $dataKedua['nis'] = '5026221002';
        $dataKedua['nama'] = 'Siti Aminah';
        $dataKedua['email'] = 'siti.aminah@example.com';
        tambah($dataKedua);
        ob_end_clean();
        
        $hasilQuery = query("SELECT * FROM siswa");
        $this->assertCount(2, $hasilQuery, "Seharusnya ada 2 data siswa di database.");
    }

    /**
     * @test
     */
    public function bisaMengubahDataSiswa()
    {
        // 1. Tambahkan data awal
        ob_start();
        tambah($this->dataSiswaValid);
        ob_end_clean();
        
        // 2. Siapkan data baru untuk diubah
        $dataUbah = $this->dataSiswaValid;
        $dataUbah['nama'] = "Budi Hartono";
        $dataUbah['jurusan'] = "Multimedia";
        $dataUbah['gambarLama'] = '';

        // 3. Panggil fungsi ubah()
        ob_start();
        $hasilUbah = ubah($dataUbah);
        ob_end_clean();
        
        $this->assertEquals(1, $hasilUbah);

        // 4. Verifikasi data di database
        $dataTerbaru = query("SELECT * FROM siswa WHERE nis = '5026221001'")[0];
        $this->assertEquals("Budi Hartono", $dataTerbaru['nama']);
        $this->assertEquals("Multimedia", $dataTerbaru['jurusan']);
    }

    /**
     * @test
     */
    public function bisaMenghapusDataSiswa()
    {
        // 1. Tambahkan data dulu untuk dihapus
        ob_start();
        tambah($this->dataSiswaValid);
        ob_end_clean();

        // 2. Verifikasi data sudah ada
        $this->assertCount(1, query("SELECT * FROM siswa WHERE nis = '5026221001'"));

        // 3. Panggil fungsi hapus()
        $hasilHapus = hapus($this->dataSiswaValid['nis']);
        $this->assertEquals(1, $hasilHapus);

        // 4. Verifikasi data sudah tidak ada
        $this->assertCount(0, query("SELECT * FROM siswa WHERE nis = '5026221001'"));
    }
}