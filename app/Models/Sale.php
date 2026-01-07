<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $primaryKey = 'SaleID';

    protected $fillable = [
        'SaleDate',
        'CustomerID',
        'TotalAmount',
        'DiscountAmount',
        'AmountPaid',
        'PaymentMethod',
        'ClerkID'
    ];

    protected $casts = [
        'SaleDate' => 'datetime',
        'TotalAmount' => 'decimal:2',
        'DiscountAmount' => 'decimal:2'
    ];

    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class, 'SaleID', 'SaleID');
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, SalesItem::class, 'SaleID', 'ProductID', 'SaleID', 'ProductID');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'ReferenceID', 'SaleID');
    }

    public function clerk(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ClerkID');
    }
}
