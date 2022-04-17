<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount','price','rating','review'
    ];

    protected $dates = ['reviewed_at'];
    
    public $timestmaps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}