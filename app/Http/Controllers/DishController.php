<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class DishController extends Controller
{

    private $cart;

    public function index(Request $request)
    {
        $this->cart = $request->user()?->cart;
        
        // TODO: Do caching, redis or client side caching
        return $this->getDishesAfterFormating(Dish::all());
    }

    private function getDishesAfterFormating($dishes)
    {
        return $dishes->map(fn($dish) => $this->formatDishForResponse($dish));
    }

    private function formatDishForResponse($dish)
    {
        if($this->isCartNotEmpty() && $this->IsCartContainsThisDish($dish->id)) {
            $dish->inCart = true;
            $dish->quantity = $this->getDishQuantity($dish->id);
        } else {
            $dish->inCart = false;
        }

        $dish->imagePath = $dish->getImagePath();

        return $dish;
    }

    private function isCartEmpty()
    {
        return $this->cart?->get()?->isEmpty();
    }

    private function isCartNotEmpty()
    {
        return !$this->isCartEmpty();
    }

    private function getDishQuantity($dishId)
    {
        return $this->cart->dishes->firstWhere('pivot.dish_id', $dishId)->pivot->quantity;
    }

    private function IsCartContainsThisDish($dishId)
    {
        return $this->cart?->dishes?->contains('pivot.dish_id', $dishId);
    }

    public function store(Request $request)
    {
        //
    }

    public function delete(Dish $dish)
    {
        //
    }
}
