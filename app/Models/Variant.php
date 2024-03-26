<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    protected $fillable = ['name', 'parent_id', 'price', 'quantity', 'product_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Variant::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('price', 'quantity')
            ->withTimestamps();
    }
}
