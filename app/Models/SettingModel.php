<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SettingModel — Key-value settings toko
 */
class SettingModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields = ['key', 'value'];

    /**
     * Ambil semua setting sebagai associative array [key => value]
     */
    public function getAllAsArray(): array
    {
        $rows = $this->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * Ambil nilai satu setting
     */
    public function getValue(string $key, string $default = ''): string
    {
        $row = $this->where('key', $key)->first();
        return $row ? (string) $row['value'] : $default;
    }

    /**
     * Simpan atau update satu setting
     */
    public function setValue(string $key, string $value): void
    {
        $existing = $this->where('key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], ['value' => $value]);
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }
}
