<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $fillable = [
        'title',
        'description',
        'image',
        'sold_count',
        'rating',
        'review_count',
        'price',
    ];

    protected $casts = [
        'on_sale' => 'boolean', //布尔类型字段
    ];

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function getImageUrlAttribute()
    {
        if(Str::startsWith($this->attributes['image'],['http://','https://'])){
            return $this->attributes['image'];
        };

        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
