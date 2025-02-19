<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AlternativeMedicineProductRequest;
use App\Models\AlternativeMedicineProduct;

class AlternativeMedicineProductController extends Controller
{
    public function index()
    {
        return response()->json(AlternativeMedicineProduct::all());
    }
    public function store(AlternativeMedicineProductRequest $request)
    {

        $data =   $request->validated();

        // الحصول على البيانات التي اجتازت التحقق
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = AlternativeMedicineProduct::create($data);

        return response()->json(['message' => 'Product added successfully', 'product' => $product]);
    }
    public function show($id)
    {
        return response()->json(AlternativeMedicineProduct::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $product = AlternativeMedicineProduct::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }
    public function destroy($id)
    {
        AlternativeMedicineProduct::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

}
