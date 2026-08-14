<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProductLocationStock;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])->latest()->get();
        return view('stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        // Get products with their stocks so we can validate quantities in frontend
        $products = Product::with('stocks')->get();
        return view('stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'cart' => 'required|string',
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return back()->with('error', 'Please add at least one product to transfer.')->withInput();
        }

        DB::beginTransaction();

        try {
            $transfer = new StockTransfer([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_date' => $request->transfer_date,
                'note' => $request->note,
            ]);
            $transfer->reference_no = 'TR-' . strtoupper(Str::random(6));
            $transfer->status = 'completed';
            $transfer->save();

            $locationId = auth()->user()->location_id;

            foreach ($cart as $item) {
                $productId = $item['id'];
                $quantity = $item['qty'];

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);

                // Decrement from source
                $fromStock = ProductLocationStock::firstOrCreate([
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'warehouse_id' => $request->from_warehouse_id
                ], ['quantity' => 0]);

                if ($fromStock->quantity < $quantity) {
                    throw new \Exception('Insufficient stock for one or more products in the source warehouse.');
                }

                $fromStock->decrement('quantity', $quantity);

                // Increment to destination
                $toStock = ProductLocationStock::firstOrCreate([
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'warehouse_id' => $request->to_warehouse_id
                ], ['quantity' => 0]);

                $toStock->increment('quantity', $quantity);
            }

            DB::commit();
            return redirect()->route('stock-transfers.index')->with('success', 'Stock transfer completed successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
