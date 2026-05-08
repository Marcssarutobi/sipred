<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Mouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request){
        $query = Product::with('category', 'user');
        $products = $query->latest()->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_barre'      => 'required|string|unique:products,code_barre',
            'nom'             => 'required|string|max:255',
            'prix_unitaire'   => 'required|numeric|min:0',
            'prix_achat'      => 'required|numeric|min:0',
            'quantite'        => 'required|integer|min:0',
            'seuil_alerte'    => 'required|integer|min:0',
            'date_expiration' => 'nullable|date',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'category_id'     => 'required|exists:categories,id',
        ]);

        // Upload image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['user_id'] = auth()->id();

        $product = Product::create($validated);
        $product->load('category', 'user');

        return response()->json($product, 200);
    }

    public function show(Product $product)
    {
        $product->load('category', 'user', 'mouvements.user');
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code_barre'      => 'required|string|unique:products,code_barre,' . $product->id,
            'nom'             => 'required|string|max:255',
            'prix_unitaire'   => 'required|numeric|min:0',
            'prix_achat'      => 'required|numeric|min:0',
            'quantite'        => 'required|integer|min:0',
            'seuil_alerte'    => 'required|integer|min:0',
            'date_expiration' => 'nullable|date',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'category_id'     => 'required|exists:categories,id',
        ]);

        // Mise à jour image
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);
        $product->load('category', 'user');

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès.']);
    }
}
