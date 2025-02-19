<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AlternativeMedicineProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        // التحقق من صحة المدخلات
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required',
            'products.*.stock' => 'required|integer|min:1', // التحقق من الكمية
        ]);
        // استرجاع المستخدم الحالي
        $user = Auth::user();
        $totalAmount = 0;
        $totalAmount = 0;

        // البدء في عملية الشراء
        DB::beginTransaction();
        try {
        
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalAmount,
                'status' => 'pending',
            ]);

            // إضافة المنتجات إلى الطلب
            foreach ($request->products as $product) {
                // تحقق من وجود المنتج في قاعدة البيانات
                $productData = AlternativeMedicineProduct::find($product['product_id']);

                // إذا لم يكن المنتج موجودًا، أوقف العملية وأرسل رسالة
                if (!$productData) {
                    return response()->json(['message' => 'Product not found for product_id: ' . $product['product_id']], 404);
                }

                // التأكد من توفر الكمية في المخزون
                if ($productData->stock < $product['quantity']) {
                    return response()->json(['message' => 'Not enough stock for product: ' . $productData->name], 400);
                }

                // إضافة العنصر إلى الطلب
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'product_id' => $productData->id,
                    'quantity' => $product['quantity'],
                    'price' => $productData->price,
                ]);

                $orderItem->save();

                // تحديث الكمية في المخزون
                $productData->stock -= $product['quantity'];
                $productData->save();

                // حساب المبلغ الإجمالي
                $totalAmount += $productData->price * $product['quantity'];
            }

            // تحديث المبلغ الإجمالي للطلب
            $order->update(['total_amount' => $totalAmount]);

            // إنهاء المعاملة
            DB::commit();

            return response()->json(['message' => 'Purchase completed successfully', 'order' => $order]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during purchase process: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }
}

