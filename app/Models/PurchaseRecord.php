<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRecord extends Model
{
    use HasFactory;

    protected $primaryKey = 'PurchaseID';

    protected $fillable = [
        'SupplierID',
        'ProductID',
        'Quantity',
        'UnitPrice',
        'TotalAmount',
        'PurchaseDate',
        'ReferenceNumber',
        'Notes'
    ];

    protected $casts = [
        'PurchaseDate' => 'datetime',
        'UnitPrice' => 'decimal:2',
        'TotalAmount' => 'decimal:2'
    ];

    /**
     * Get the supplier that made the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    /**
     * Get the product that was purchased.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    /**
     * Get the transactions associated with this purchase.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'ReferenceID', 'PurchaseID')
            ->where('ReferenceType', 'purchase');
    }
} 