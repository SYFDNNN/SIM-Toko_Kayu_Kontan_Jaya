<?php namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

/**
 * TransactionSeeder — 15 transaksi demo dengan detail & stock movements
 */
class TransactionSeeder extends Seeder {
    public function run(): void {
        $this->db->table('transactions')->truncate();
        $this->db->table('transaction_details')->truncate();
        $this->db->table('stock_movements')->truncate();

        $txns = [
            // [no, user_id, customer, total, payment, product_id, qty, unit_price, days_ago]
            ['TRX-D01',2,'Pak Hendra', 1350000,1500000,  1,1,1350000,28],
            ['TRX-D02',2,'Bu Wati',    750000, 750000,   3,1,750000, 26],
            ['TRX-D03',3,'Pak Doni',   4800000,5000000,  2,1,4800000,24],
            ['TRX-D04',2,null,         1650000,2000000,  4,1,1650000,22],
            ['TRX-D05',2,'Ibu Sari',   2600000,2600000,  5,1,2600000,20],
            ['TRX-D06',3,'Pak Bimo',   980000, 1000000,  6,1,980000, 18],
            ['TRX-D07',2,'Toko Jaya',  8500000,8500000,  8,1,8500000,16],
            ['TRX-D08',2,null,         750000, 800000,   3,1,750000, 14],
            ['TRX-D09',3,'Bu Lina',    1900000,2000000,  7,1,1900000,12],
            ['TRX-D10',2,'Pak Yusuf',  750000, 750000,   3,1,750000, 10],
            ['TRX-D11',2,null,         980000, 1000000,  6,1,980000, 8],
            ['TRX-D12',3,'Bu Rina',    1350000,1350000,  1,1,1350000,6],
            ['TRX-D13',2,'Pak Fajar',  1800000,2000000,  1,1,1350000,4],
            ['TRX-D14',2,null,         750000, 800000,   3,1,750000, 2],
            ['TRX-D15',3,'Bu Dewi',    1650000,2000000,  4,1,1650000,1],
        ];

        foreach ($txns as $t) {
            [$no,$uid,$cust,$total,$paid,$pid,$qty,$uprice,$dago] = $t;
            $date = date('Y-m-d H:i:s', strtotime("-{$dago} days"));

            $this->db->table('transactions')->insert([
                'transaction_no'   => $no,
                'user_id'          => $uid,
                'customer_name'    => $cust,
                'subtotal'         => $total,
                'discount'         => 0,
                'total'            => $total,
                'payment_method'   => 'cash',
                'payment_amount'   => $paid,
                'change_amount'    => $paid - $total,
                'status'           => 'completed',
                'transaction_date' => $date,
                'created_at'       => $date,
                'updated_at'       => $date,
            ]);
            $tid = $this->db->insertID();

            $this->db->table('transaction_details')->insert([
                'transaction_id' => $tid,
                'product_id'     => $pid,
                'quantity'       => $qty,
                'unit_price'     => $uprice,
                'subtotal'       => $uprice * $qty,
            ]);

            // Stock movement OUT
            $product = $this->db->table('products')->where('id', $pid)->get()->getRowArray();
            $before  = $product['stock'] ?? 10;
            $after   = max(0, $before - $qty);

            $this->db->table('stock_movements')->insert([
                'product_id'     => $pid,
                'user_id'        => $uid,
                'type'           => 'OUT',
                'quantity'       => -$qty,
                'stock_before'   => $before,
                'stock_after'    => $after,
                'reference_type' => 'transaction',
                'reference_id'   => $tid,
                'reference_no'   => $no,
                'notes'          => 'POS Checkout',
                'created_at'     => $date,
            ]);
        }
    }
}
