<?php namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder {
    public function run(): void {
        $this->db->table('products')->truncate();
        $now = date('Y-m-d H:i:s');
        $this->db->table('products')->insertBatch([
            ['category_id'=>1,'sku'=>'MJK-001','name'=>'Meja Kerja Jati Minimalis',    'description'=>'Meja kerja kayu jati solid, ukuran 120x60x75cm','cost_price'=>850000, 'selling_price'=>1350000,'stock'=>12,'stock_minimum'=>3,'lead_time_days'=>5,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>1,'sku'=>'MJM-002','name'=>'Meja Makan 6 Kursi Mahoni',    'description'=>'Set meja makan kayu mahoni, finishing natural',  'cost_price'=>3200000,'selling_price'=>4800000,'stock'=>4, 'stock_minimum'=>2,'lead_time_days'=>7,'unit'=>'set','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>2,'sku'=>'KRS-001','name'=>'Kursi Santai Rotan Kombinasi', 'description'=>'Kursi santai rangka kayu + anyaman rotan',       'cost_price'=>450000, 'selling_price'=>750000, 'stock'=>20,'stock_minimum'=>5,'lead_time_days'=>3,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>2,'sku'=>'KJT-002','name'=>'Kursi Jati Antik Ukir',        'description'=>'Kursi kayu jati ukiran tangan, motif bunga',     'cost_price'=>980000, 'selling_price'=>1650000,'stock'=>6, 'stock_minimum'=>3,'lead_time_days'=>5,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'sku'=>'LMR-001','name'=>'Lemari Pakaian 3 Pintu Pinus', 'description'=>'Lemari 3 pintu kayu pinus, warna natural',       'cost_price'=>1750000,'selling_price'=>2600000,'stock'=>3, 'stock_minimum'=>2,'lead_time_days'=>7,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>3,'sku'=>'RBK-002','name'=>'Rak Buku 5 Susun Jati',        'description'=>'Rak buku kayu jati, 5 tingkat adjustable',       'cost_price'=>620000, 'selling_price'=>980000, 'stock'=>8, 'stock_minimum'=>3,'lead_time_days'=>5,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'sku'=>'TPT-001','name'=>'Tempat Tidur Kayu Jati Single', 'description'=>'Dipan single 90x200cm, kayu jati solid',        'cost_price'=>1200000,'selling_price'=>1900000,'stock'=>5, 'stock_minimum'=>2,'lead_time_days'=>7,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>4,'sku'=>'TPT-002','name'=>'Set Kamar Tidur Mahoni Queen',  'description'=>'Dipan queen + 2 nakas + lemari, mahoni',        'cost_price'=>5800000,'selling_price'=>8500000,'stock'=>2, 'stock_minimum'=>1,'lead_time_days'=>14,'unit'=>'set','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>5,'sku'=>'AKS-001','name'=>'Nakas Kayu Mahoni',            'description'=>'Nakas/meja samping tempat tidur, 1 laci',       'cost_price'=>280000, 'selling_price'=>450000, 'stock'=>15,'stock_minimum'=>5,'lead_time_days'=>3,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['category_id'=>5,'sku'=>'AKS-002','name'=>'Cermin Kayu Bingkai Jati',     'description'=>'Cermin dinding frame kayu jati ukiran',         'cost_price'=>380000, 'selling_price'=>620000, 'stock'=>2, 'stock_minimum'=>3,'lead_time_days'=>5,'unit'=>'pcs','is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}
