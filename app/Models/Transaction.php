<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $primaryKey = 'TransactionID';

    protected $fillable = [
        'ProductID',
        'TransactionType',
        'QuantityChange',
        'UnitPrice',
        'TotalAmount',
        'ReferenceID',
        'TransactionDate'
    ];

    protected $casts = [
        'QuantityChange' => 'integer',
        'UnitPrice' => 'decimal:2',
        'TotalAmount' => 'decimal:2',
        'TransactionDate' => 'datetime'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'ReferenceID', 'SaleID');
    }

    /**
     * Get the purchase record associated with this transaction.
     */
    public function purchase()
    {
        return $this->belongsTo(PurchaseRecord::class, 'ReferenceID', 'PurchaseID');
    }
}
