<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|string',
            'price' => 'required|integer',
        ]);

        $product = Product::create($request->all());

        return response()->json(['message' => 'Product created', 'data' => $product], 201);
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('image');
        $filename = time() . '-' . preg_replace('/\s+/', '-', $file->getClientOriginalName());
        $file->storeAs('public/images', $filename);

        return response()->json([
            'path' => '/storage/images/' . $filename,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|string',
            'price' => 'required|integer',
        ]);

        $product->update($request->all());

        return response()->json(['message' => 'Product updated', 'data' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
