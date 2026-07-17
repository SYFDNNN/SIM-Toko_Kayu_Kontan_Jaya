<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder — Master seeder yang menjalankan semua seeder
 * Jalankan: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan dalam urutan yang benar (foreign key dependencies)
        $this->call('UserSeeder');
        $this->call('CategorySeeder');
        $this->call('ProductSeeder');
        $this->call('TransactionSeeder');

        echo "✅ Demo data seeding selesai!\n";
        echo "   - 3 users\n";
        echo "   - 5 categories\n";
        echo "   - 10 products\n";
        echo "   - 15 transactions\n";
    }
}
