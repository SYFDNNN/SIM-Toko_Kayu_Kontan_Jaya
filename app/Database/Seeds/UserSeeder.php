<?php namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder {
    public function run(): void {
        $this->db->table('users')->truncate();
        $this->db->table('users')->insertBatch([
            ['name'=>'Administrator', 'email'=>'admin@tokokayukontan.com',  'password'=>password_hash('admin123',    PASSWORD_BCRYPT), 'role'=>'admin',   'is_active'=>1,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Budi Santoso',  'email'=>'owner@tokokayukontan.com',  'password'=>password_hash('password123', PASSWORD_BCRYPT), 'role'=>'owner',   'is_active'=>1,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Siti Rahayu',   'email'=>'kasir1@tokokayukontan.com', 'password'=>password_hash('password123', PASSWORD_BCRYPT), 'role'=>'cashier', 'is_active'=>1,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Ahmad Fauzi',   'email'=>'kasir2@tokokayukontan.com', 'password'=>password_hash('password123', PASSWORD_BCRYPT), 'role'=>'cashier', 'is_active'=>1,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
        ]);
    }
}
