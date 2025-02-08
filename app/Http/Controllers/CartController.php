<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\EditCartItemQuantityRequest;
use App\Models\Cart;
use App\Models\Dish;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request)
    {

        $dishId = $request->input('dishId');
        $user = $request->user();

        $cart = $user?->cart ?? Cart::create(['user_id' => $user->id]);

        $dish = Dish::find($dishId);

        if(!$dish) {
            return response()->json([
                'message' => 'Dish not found'
            ], 404);
        }

        $cart->dishes()->syncWithoutDetaching([
            $dishId => ['total' => $dish->price,]
        ]);

        return response()->json([
         'message' => 'item added to cart',
        ]);
    }

    public function editQuantity(EditCartItemQuantityRequest $request)
    {

        $dishId = $request->input('dishId');
        $quantity = $request->input('quantity');

        $cart = $request->user()->cart;
        $dish = $cart->dishes()->firstWhere('dish_id', $dishId);

        if (!$dish) {
            return response()->json(['message' => 'Dish not found in cart'], 404);
        }

        $cart->dishes()->updateExistingPivot($dishId, [
            'quantity' => $dish->pivot->quantity + $quantity,
            'total' => ($dish->pivot->quantity + $quantity) * $dish->price,
        ]);

        return response()->json(['message' => 'Quantity updated successfully']);
    }

    public function getCartItems(Request $request)
    {
        $cartItems = $request->user()?->cart?->dishes;

        if($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 404);
        }

        $cartItems->each(function($cartItem) {
            $cartItem->imagePath = $cartItem->getImagePath();
        });
        
        return $cartItems;
    }

    public function removeCartItem(Request $request, $id)
    {
        $cart = $request->user()?->cart;

        if(!$cart || $cart->isEmpty()) {
            return response()->json([
                'message' => 'Cart not found'
            ], 404);
        }

        $cart->dishes()->detach($id);

        return $this->getCartItems($request);
    }

    public function getNumberOfItems(Request $request) {
        $numberOfItems = $request->user()?->cart?->dishes?->count();
        
        return is_null($numberOfItems)
            ? response()->json(['error' => 'Cart is empty',], 404)
            : response()->json(['numberOfItems' => $numberOfItems]);
    }
}
