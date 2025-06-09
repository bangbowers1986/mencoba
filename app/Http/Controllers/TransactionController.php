<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem; // Pastikan model ini ada
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function sync(Request $request)
    {
        $validated = $request->validate([
            '*.local_id' => 'required|string',
            '*.total' => 'required|numeric',
            '*.items' => 'required|json',
            '*.user_id' => 'required|exists:users,id',
            '*.status' => 'sometimes|string' // Tambahkan validasi untuk status
        ]);

        $syncedIds = [];
        foreach ($validated as $trx) {
            $transaction = Transaction::updateOrCreate(
                ['local_id' => $trx['local_id']],
                [
                    'user_id' => $trx['user_id'],
                    'total' => $trx['total'],
                    'items' => $trx['items'],
                    'status' => $trx['status'] ?? 'completed' // Default value
                ]
            );
            $syncedIds[] = $transaction->id;
        }

        return response()->json(['synced_ids' => $syncedIds]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local_id' => 'required|string|unique:transactions,local_id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ], [
            'local_id.required' => 'Transaction ID (local_id) wajib disertakan',
            'local_id.unique' => 'Transaction ID sudah digunakan',
            'items.required' => 'Daftar item transaksi wajib disertakan',
            'items.*.product_id.required' => 'Product ID wajib disertakan',
            'items.*.product_id.exists' => 'Product tidak ditemukan',
            'items.*.quantity.required' => 'Quantity wajib disertakan',
            'items.*.quantity.integer' => 'Quantity harus berupa angka',
            'items.*.quantity.min' => 'Quantity minimal 1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $total = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => 'Stok produk tidak mencukupi',
                        'product_id' => $product->id,
                        'available_stock' => $product->stock
                    ], 400);
                }

                $product->decrement('stock', $item['quantity']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            $transaction = Transaction::create([
                'local_id' => $request->local_id,
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'completed',
                'items' => json_encode($items) // Simpan items sebagai JSON
            ]);

            // Jika menggunakan tabel terpisah untuk items
            if (class_exists('App\Models\TransactionItem')) {
                $transaction->transactionItems()->createMany($items);
            }

            return response()->json([
                'message' => 'Transaksi berhasil dibuat',
                'data' => [
                    'transaction' => $transaction,
                    'items' => $items
                ]
            ], 201);
        });
    }
}