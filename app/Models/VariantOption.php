<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantOption extends Model
{
    use HasFactory;

    protected  $fillable = [
        'name_variant',
        'value_variant',
        'variantable_id',
        'variantable_type'
    ];

    public function  variantable(): MorphTo
    {
        return  $this->morphTo();
    }
}
