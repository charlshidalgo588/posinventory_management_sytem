<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesItem extends Model
{
    /** @use HasFactory<\Database\Factories\SalesItemFactory> */
    use HasFactory;
    protected $primaryKey = 'SalesItemID';

    protected $fillable = [
        'SaleID',
        'ProductID',
        'Quantity',
        'PriceAtSale'
    ];

    protected $casts = [
        'Quantity' => 'integer',
        'PriceAtSale' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'SaleID', 'SaleID');
    }
}
