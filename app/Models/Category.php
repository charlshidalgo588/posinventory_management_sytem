<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $primaryKey = 'CategoryID';

    protected $fillable = [
        'CategoryName'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'CategoryID', 'CategoryID');
    }
}
