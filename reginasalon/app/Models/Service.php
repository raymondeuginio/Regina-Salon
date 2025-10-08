<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'store_id',
        'name',
        'description',
        'duration',
        'price',
    ];

    /**
     * @return BelongsTo<ServiceCategory, Service>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * @return BelongsTo<Store, Service>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
