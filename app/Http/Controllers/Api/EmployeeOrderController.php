<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class EmployeeOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('orderItems.product', 'client')->where('status', $request->status)->get();

        return response()->json(['orders' => $orders]);
    }

    public function prepare($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'Pending') {
            return response()->json([
                'message' => "Cannot prepare an order with status {$order->status}.",
            ], 422);
        }

        $order->update(['status' => 'Preparing']);

        return response()->json(['message' => 'Order is now being prepared.']);
    }

    public function deliver($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'Preparing') {
            return response()->json([
                'message' => "Cannot deliver an order with status \"{$order->status}\".",
            ], 422);
        }

        $order->update(['status' => 'Delivered']);

        return response()->json(['message' => 'Order marked as delivered.']);
    }
}