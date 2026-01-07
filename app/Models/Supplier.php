<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Supplier extends Model
{
    use HasFactory;

    /** -----------------------------------------
     *  Database table & primary key
     * ----------------------------------------- */
    protected $table = 'suppliers';
    protected $primaryKey = 'SupplierID';
    public $timestamps = true;

    /** -----------------------------------------
     *  Mass-assignable fields
     * ----------------------------------------- */
    protected $fillable = [
        'SupplierName',
        'ContactNumber',
        'Email',
        'Address',
    ];

    /** -----------------------------------------
     *  Relationship: Supplier → ProductSupplier
     * ----------------------------------------- */
    public function productSuppliers(): HasMany
    {
        return $this->hasMany(
            ProductSupplier::class,
            'SupplierID',
            'SupplierID'
        );
    }

    /** -----------------------------------------
     *  Relationship: Supplier → Products (via pivot)
     * ----------------------------------------- */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,           // Final model
            ProductSupplier::class,   // Pivot model
            'SupplierID',             // FK on product_suppliers
            'ProductID',              // FK on products
            'SupplierID',             // Local PK
            'ProductID'               // Pivot → Product link
        );
    }
}
