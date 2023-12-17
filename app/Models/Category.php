<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
        // ->select('id', 'name', 'slug', 'product_price', 'product_image', 'product_stock');
    }
}

