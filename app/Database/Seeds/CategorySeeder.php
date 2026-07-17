<?php namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder {
    public function run(): void {
        $this->db->table('categories')->truncate();
        $this->db->table('categories')->insertBatch([
            ['name'=>'Meja',         'slug'=>'meja',          'description'=>'Berbagai jenis meja kayu',        'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Kursi',        'slug'=>'kursi',         'description'=>'Kursi kayu jati dan mahoni',      'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Lemari',       'slug'=>'lemari',        'description'=>'Lemari pakaian dan rak buku',     'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Tempat Tidur', 'slug'=>'tempat-tidur',  'description'=>'Dipan dan set kamar tidur',       'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Aksesori',     'slug'=>'aksesori',      'description'=>'Aksesori dan furnitur kecil',     'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
        ]);
    }
}
