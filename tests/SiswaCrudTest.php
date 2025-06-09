<?php
// File: tests/SiswaCrudTest.php

namespace Tests;

class SiswaCrudTest extends DatabaseTestCase
{
    private array $dataSiswaValid;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->dataSiswaValid = [
            'nis' => '5026221001', 'nama' => 'Budi Santoso', 'tmpt_Lahir' => 'Surabaya',
            'tgl_Lahir' => '2004-05-10', 'jekel' => 'Laki - Laki', 'jurusan' => 'Rekayasa Perangkat Lunak',
            'ipk' => 3.75, 'jalur_masuk' => 'SNBT', 'nilai' => 90,
            'email' => 'budi.santoso@example.com', 'alamat' => 'Jl. Kenjeran No. 123'
        ];
        $_FILES = [];
    }
    
    /** @test */
    public function bisa_menambah_dan_mengambil_data_siswa(): void
    {
        ob_start();
        $hasilTambah = tambah($this->dataSiswaValid);
        ob_end_clean();
        
        $this->assertEquals(1, $hasilTambah);

        $dataDariDB = query("SELECT * FROM siswa WHERE nis = '5026221001'");
        $this->assertCount(1, $dataDariDB);
        $this->assertEquals('Budi Santoso', $dataDariDB[0]['nama']);
    }

    /** @test */
    public function bisa_mengubah_data_siswa(): void
    {
        ob_start();
        tambah($this->dataSiswaValid);
        $dataUbah = $this->dataSiswaValid;
        $dataUbah['nama'] = "Budi Hartono";
        $dataUbah['gambarLama'] = '';
        $hasilUbah = ubah($dataUbah);
        ob_end_clean();
        
        $this->assertEquals(1, $hasilUbah);
        $dataTerbaru = query("SELECT nama FROM siswa WHERE nis = '5026221001'")[0];
        $this->assertEquals("Budi Hartono", $dataTerbaru['nama']);
    }

    /** @test */
    public function bisa_menghapus_data_siswa(): void
    {
        ob_start();
        tambah($this->dataSiswaValid);
        ob_end_clean();

        $hasilHapus = hapus($this->dataSiswaValid['nis']);
        echo $hasilHapus;
        $this->assertEquals(1, $hasilHapus);

        $dataSetelahHapus = query("SELECT * FROM siswa WHERE nis = '5026221001'");
        $this->assertCount(0, $dataSetelahHapus);
    }
}
