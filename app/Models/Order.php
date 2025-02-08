<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'user_id',
        'firstName',
        'lastName',
        'email',
        'street',
        'city',
        'state',
        'zipCode',
        'country',
        'phone',
        'montant',
        'numberOfItems',
        'checkout_session_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class)->withPivot(['quantity']);
    }
}
