<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $items = $request->input('items');

        $products = [];
        foreach ($items as $item) {
            $product = Product::where('slug', $item['slug'])->where('is_available', true)->first();

            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'message' => "Not enough stock for \"{$product->name}\". Available: {$product->stock}",
                ], 422);
            }

            $products[] = [
                'product'  => $product,
                'quantity' => $item['quantity'],
            ];
        }

        $order = DB::transaction(function () use ($products) {
            $order = Order::create([
                'client_id' => auth()->id(),
            ]);

            foreach ($products as $entry) {
                $order->orderItems()->create([
                    'product_id' => $entry['product']->id,
                    'quantity'   => $entry['quantity'],
                    'price'      => $entry['product']->price,
                ]);

                $entry['product']->decrement('stock', $entry['quantity']);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Order placed successfully.',
            'order'   => $order->load('orderItems.product'),
        ], 201);
    }

    public function order_status($id)
    {
        $order = Order::where('client_id', auth()->id())->findOrFail($id);

        return response()->json([
            'order_status' => $order->status,
        ]);
    }

    public function cancel($id)
    {
        $order = Order::where('client_id', auth()->id())->findOrFail($id);

        if ($order->status !== 'Pending') {
            return response()->json([
                'message' => 'Order cannot be cancelled. Only pending orders can be cancelled.',
            ], 422);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'Cancelled']);
        });

        return response()->json(['message' => 'Order cancelled successfully.']);
    }
}