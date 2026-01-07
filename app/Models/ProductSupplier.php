<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplier extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSupplierFactory> */
    use HasFactory;
    protected $primaryKey = 'ProductSupplierID';

    protected $fillable = [
        'ProductID',
        'SupplierID',
        'PurchasePrice',
        'SupplierProductCode',
        'LeadTime',
        'MinimumOrderQuantity'
    ];

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}
