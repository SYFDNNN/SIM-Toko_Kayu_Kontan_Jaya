<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CategoryModel
 */
class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields = ['name', 'slug', 'description'];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'slug' => 'required|min_length[2]|max_length[120]',
    ];
}
