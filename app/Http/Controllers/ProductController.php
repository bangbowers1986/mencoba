<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        return response()->json(Product::all());
    }

    // Create new product
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }
}