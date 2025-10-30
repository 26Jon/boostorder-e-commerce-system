<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;

class OrderController extends Controller
{
    //GET api/order/user/{userId}
    public function getOrdersByUserId($userId){
        try {
            $orders = Orders::where("user_id", $userId)->with('orderItems.product')->get();

            if($orders->isEmpty()){
                return response()->json(['message' => 'Order not found for this user'], 404);
            }

            $formattedOrders = $orders->map(function($order){
                return [
                    'id' => $order->id,
                    'user_id'=> $order->user_id,
                    'total_price'=>$order->total_price,
                    'status'=> $order->status,
                    'order_items'=> $order->orderItems->map(function($item){
                        return [
                            'quantity'=> $item->quantity,
                            'subtotal'=>(float)$item->product->price * $item->quantity,
                            'product'=>[
                                'name'=>$item->product->name,
                                'price'=>$item->product->price,
                            ],
                        ];
                    }),
                ];
            });
            return response()->json($formattedOrders);
        }
        catch(\Exception $e){
            return response()->json(['message'=> 'Could not get order for this user'],500);
        }
    }

    //PUT api/order/{orderId}/status
    public function updateOrderStatus($orderId, Request $request){

        $request->validate([
            'status' => 'required|string|in:processing,completed'
        ]);

        try{
            $order = Orders::find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $order->status = $request->status;
            $order->save();

            return response()->json(['message' => 'Order status updated successfully']);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Could not update order status'], 500);
        }
    }
}
