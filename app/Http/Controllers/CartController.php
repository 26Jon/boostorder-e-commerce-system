<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carts;
use App\Models\User;
use App\Models\CartItems;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //GET api/cart/user/{userId}
    public function getCartByUserId($userId){
        try{
            $user = User::with('cart.cartItems.product')->find($userId);

            if(!$user || !$user->cart){
                return response()->json(['error' => 'Cart not found'], 404);
            }

            $cart=$user->cart;
            
            $totalPrice = $cart->cartItems->sum(function($item){
                return $item->quantity * ($item->product->price ?? 0);
            });

            $cart->update(['total_price' => $totalPrice]);

            $cart->totalPrice= round($totalPrice, 2);

            $cartItems = $cart->cartItems->map(function($item){
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => round($item->quantity * $item->product->price, 2)
                ];
            });

            return response()->json([
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'total_price' => round($totalPrice, 2),
                'items' => $cartItems
            ]);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Could not retrieve cart items'], 500);
        }
    }

    //POST api/cart/add
    public function addToCart(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try{
            $cart = Carts::firstOrCreate(
                ['user_id' => $request->user_id],
                ['total_price' => 0]
            );

            $cartItem = CartItems::where('cart_id', $cart->id)->where('product_id', $request->product_id)->first();

            if($cartItem){
                $cartItem->quantity += $request->input('quantity',1);
                $cartItem->save();
            } else {
                $cartItem = CartItems::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->input('quantity', 1)
                ]);
            }

            $cart->load('cartItems.product');

            $totalPrice = $cart->cartItems->sum(function($item){
                return $item->quantity * $item->product->price;
            });

            $cart->update(['total_price' => $totalPrice]);

            return response()->json(['message' => 'Product added to cart successfully'], 200);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to add product to cart'], 500);
        }
    }

    //POST api/cart/checkout
    public function checkoutCart(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try{
            $cart = Carts::with('cartItems.product')->where('user_id', $request->user_id)->first();
            
            if(!$cart || $cart->cartItems->isEmpty()){
                return response()->json(['error' => 'Cart is empty'], 404);
            }

            DB::beginTransaction();

            try {
                $totalPrice = $cart->cartItems->sum(function ($item) {
                    return $item->quantity * ($item->product->price ?? 0);
                });

                $order = Orders::create([
                    'user_id' => $request->user_id,
                    'status' => 'processing',
                    'total_price' => $totalPrice,
                ]);

                foreach ($cart->cartItems as $cartItem) {
                    OrderItems::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->price,
                    ]);
                }

                $cart->cartItems()->delete();
                $cart->update(['total_price' => 0]);

                DB::commit();

                return response()->json([
                    'message' => 'Checkout successful! Order created.',
                    'order' => $order->load('orderItems.product')
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Checkout failed.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
        catch (\Throwable $e) {
            return response()->json(['error' => 'Checkout process failed'], 500);
        }
    }

    //PUT api/user/{userId}/cart/{cartItemId}
    public function updateQuantity(Request $request, $userId, $cartItemId){
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $cart = Carts::where('user_id', $userId)->first();

            if (!$cart) {
                return response()->json(['message' => 'Cart not found for this user.'], 404);
            }

            $cartItem = $cart->cartItems()->where('id', $cartItemId)->first();

            if (!$cartItem) {
                return response()->json(['message' => 'Cart item not found.'], 404);
            }

            if ($request->quantity == 0) {
                $cartItem->delete();
                return response()->json(['message' => 'Cart item removed successfully.']);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            $totalPrice = $cart->cartItems->sum(function ($item) {
                return $item->quantity * ($item->product->price ?? 0);
            });

            $cart->update(['total_price' => $totalPrice]);

            return response()->json(['message' => 'Cart item updated successfully.']);
        } 
        catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to update cart item'], 500);
        }
    }

    //DELETE api/user/{userId}/cart/{cartItemId}
    public function removeCartItem($userId, $cartItemId){
        try{
            $cart = Carts::where('user_id', $userId)->first();

            if(!$cart){
                return response()->json(['error' => 'Cart not found'], 404);    
            }

            $cartItem = $cart->cartItems()->where('id', $cartItemId)->first();

            if (!$cartItem) {
                return response()->json([
                    'message' => 'Cart item not found for this user\'s cart.'
                ], 404);
            }

            $cartItem->delete();

            $cart->load('cartItems.product');

            $totalPrice = $cart->cartItems->sum(function ($item) {
                return $item->quantity * ($item->product->price ?? 0);
            });

            $cart->update(['total_price' => $totalPrice]);

            return response()->json([
                'message' => 'Cart item removed successfully.'        
            ], 200);
        }
        catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to remove cart item'], 500);
        }
    }
}
