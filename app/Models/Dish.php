<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dish extends Model
{

    use HasFactory;

    protected $fillable = [
        'dish_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class)->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function getImagePath()
    {
        return url('storage/dishes/' . $this->imageName);
    }
}
