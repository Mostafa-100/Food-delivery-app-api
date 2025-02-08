<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $fillable = [
        'user_id',
        'cart_id',
    ];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class)->withPivot(['quantity', 'total']);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
