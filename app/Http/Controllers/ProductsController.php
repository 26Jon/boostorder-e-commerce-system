<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class ProductsController extends Controller
{
    //GET api/products
    public function getAllProducts(){
        try{
            $products = Products::all();
            return response()->json($products);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve products']);
        }
    }

    //GET api/product/{id}
    public function getProductById($id){
        try{
            $product = Products::find($id);
            if($product){
                return response()->json($product);
            } else {
                return response()->json(['message' => 'Product not found']);   
            }
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve product']);
        }
    }
}
