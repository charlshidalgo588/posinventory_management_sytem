<?php
/**
 * Inventory Model
 *
 * Represents the inventory management system for products.
 * Handles stock levels, reorder points, and inventory status.
 *
 * @category Models
 * @package  App\Models
 * @author   ChangChang Team <team@changchang.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/changchang
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Inventory
 *
 * Manages product inventory including stock levels and reorder points.
 *
 * @category Models
 * @package  App\Models
 * @author   ChangChang Team <team@changchang.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/changchang
 */
class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory;
    protected $primaryKey = 'InventoryID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ProductID',
        'QuantityOnHand',
        'LastUpdated',
        'Status',
        'MinimumStock',
        'MaximumStock',
        'ReorderLevel'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'LastUpdated' => 'datetime',
        'QuantityOnHand' => 'integer',
        'MinimumStock' => 'integer',
        'MaximumStock' => 'integer',
        'ReorderLevel' => 'integer'
    ];

    /**
     * Get the product that owns the inventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}
