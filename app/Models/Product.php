<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'ProductID';

    /**
     * ============================
     * MASS ASSIGNABLE FIELDS
     * ============================
     */
    protected $fillable = [
        // BASIC INFORMATION
        'ProductName',
        'Unit',
        'IsReturnable',

        // CLASSIFICATION
        'CategoryID',
        'SupplierID',

        // PRODUCT DETAILS
        'Brand',
        'SKU',
        'Description',

        // SPECIFICATIONS (✅ ADDED)
        'Material',
        'ProfileType',
        'Color',
        'Length',
        'Width',
        'Thickness',

        // WEIGHT
        'Weight',
        'WeightUnit',

        // PRICING
        'SellingPrice',
        'CostPrice',

        // INVENTORY (INITIAL VALUES)
        'OpeningStock',
        'ReorderLevel',

        // IMAGE
        'Product_Image',
    ];

    /**
     * ============================
     * RELATIONSHIPS
     * ============================
     */

    /**
     * Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'CategoryID',
            'CategoryID'
        );
    }

    /**
     * Inventory (current stock, reorder level)
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(
            Inventory::class,
            'ProductID',
            'ProductID'
        );
    }

    /**
     * Suppliers (pivot table)
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            Supplier::class,
            'product_suppliers',
            'ProductID',
            'SupplierID'
        );
    }

    /**
     * Sales items
     */
    public function salesItems(): HasMany
    {
        return $this->hasMany(
            SalesItem::class,
            'ProductID',
            'ProductID'
        );
    }

    /**
     * Inventory transactions / logs
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(
            Transaction::class,
            'ProductID',
            'ProductID'
        );
    }
}
