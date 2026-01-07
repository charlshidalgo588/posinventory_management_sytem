<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $primaryKey = 'LogID';
    
    protected $fillable = [
        'ProductID',
        'type',
        'quantity',
        'notes',
        'created_by'
    ];

    /**
     * Get the product that owns the inventory log.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }

    /**
     * Get the user who created the inventory log.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 