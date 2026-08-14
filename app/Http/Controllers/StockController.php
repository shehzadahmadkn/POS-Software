<?php

namespace App\Http\Controllers;

use App\Models\ProductLocationStock;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $locationId = auth()->user()->location_id; // Fix IDOR
        $filter = $request->input('filter');
        $warehouseId = $request->input('warehouse_id'); // Added warehouse filter

        if ($locationId) {
            $query = ProductLocationStock::where('location_id', $locationId)->with(['product.category', 'warehouse']);
            if ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
            if ($filter === 'zero') {
                $query->where('quantity', 0);
            } elseif ($filter === 'above_zero') {
                $query->where('quantity', '>', 0);
            } elseif ($filter === 'below_zero') {
                $query->where('quantity', '<', 0);
            }
        } else {
            $query = ProductLocationStock::select('product_id', 'warehouse_id', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('product_id', 'warehouse_id')
                ->with(['product.category', 'warehouse']);
            if ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
            if ($filter === 'zero') {
                $query->having('quantity', '=', 0);
            } elseif ($filter === 'above_zero') {
                $query->having('quantity', '>', 0);
            } elseif ($filter === 'below_zero') {
                $query->having('quantity', '<', 0);
            }
        }

        $stocks = $query->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();

        return view('stocks.index', compact('stocks', 'filter', 'products', 'warehouses', 'warehouseId'));
    }

    public function details(Request $request)
    {
        $productId = $request->input('product_id');
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $locationId = $request->input('location_id'); // Optional location filter (legacy)
        $warehouseId = $request->input('warehouse_id');

        $logs = [];

        // 1. Fetch Sales
        $salesQuery = \App\Models\SaleItem::where('product_id', $productId)
            ->with(['sale.location']);

        if ($locationId) {
            $salesQuery->whereHas('sale', function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        if ($warehouseId) {
            $salesQuery->where('warehouse_id', $warehouseId);
        }
        if ($from) {
            $salesQuery->whereHas('sale', function($q) use ($from) {
                $q->where('transaction_date', '>=', $from . ' 00:00:00');
            });
        }
        if ($to) {
            $salesQuery->whereHas('sale', function($q) use ($to) {
                $q->where('transaction_date', '<=', $to . ' 23:59:59');
            });
        }

        foreach ($salesQuery->get() as $item) {
            if ($item->sale) {
                $logs[] = [
                    'date' => $item->sale->transaction_date,
                    'type' => 'Sale',
                    'ref' => 'SALE-' . $item->sale_id,
                    'warehouse' => $item->warehouse->name ?? ($item->sale->location->name ?? 'N/A'),
                    'qty' => -$item->quantity
                ];
            }
        }

        // 2. Fetch Purchases
        $purchasesQuery = \App\Models\PurchaseItem::where('product_id', $productId)
            ->with(['purchase.location']);

        if ($locationId) {
            $purchasesQuery->whereHas('purchase', function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        if ($warehouseId) {
            $purchasesQuery->where('warehouse_id', $warehouseId);
        }
        if ($from) {
            $purchasesQuery->whereHas('purchase', function($q) use ($from) {
                $q->where('transaction_date', '>=', $from . ' 00:00:00');
            });
        }
        if ($to) {
            $purchasesQuery->whereHas('purchase', function($q) use ($to) {
                $q->where('transaction_date', '<=', $to . ' 23:59:59');
            });
        }

        foreach ($purchasesQuery->get() as $item) {
            if ($item->purchase) {
                $logs[] = [
                    'date' => $item->purchase->transaction_date,
                    'type' => 'Purchase',
                    'ref' => 'PUR-' . $item->purchase_id,
                    'warehouse' => $item->warehouse->name ?? ($item->purchase->location->name ?? 'N/A'),
                    'qty' => $item->quantity
                ];
            }
        }

        // 3. Fetch Stock Adjustments
        $adjQuery = \App\Models\StockAdjustment::where('product_id', $productId)
            ->with(['location']);

        if ($locationId) {
            $adjQuery->where('location_id', $locationId);
        }
        // Assuming StockAdjustment doesn't have warehouse_id yet, fallback to location or nothing
        if ($from) {
            $adjQuery->where('date', '>=', $from . ' 00:00:00');
        }
        if ($to) {
            $adjQuery->where('date', '<=', $to . ' 23:59:59');
        }

        foreach ($adjQuery->get() as $adj) {
            $logs[] = [
                'date' => $adj->date,
                'type' => 'Adjustment',
                'ref' => 'ADJ-' . $adj->id . ($adj->reason ? ' (' . $adj->reason . ')' : ''),
                'warehouse' => $adj->location->name ?? 'N/A',
                'qty' => $adj->type === 'addition' ? $adj->quantity : -$adj->quantity
            ];
        }

        // Sort logs by date descending (latest first)
        usort($logs, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    public function adjustmentList()
    {
        $locationId = auth()->user()->location_id;
        $adjustments = StockAdjustment::where('location_id', $locationId)
            ->with(['product', 'location'])
            ->orderBy('id', 'desc')
            ->get();

        $products = Product::where('is_active', true)->get();

        return view('stocks.adjustment', compact('adjustments', 'products'));
    }

    public function adjustStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'date' => 'required|date'
        ]);

        $locationId = auth()->user()->location_id;

        DB::beginTransaction();
        try {
            // Log adjustment
            StockAdjustment::create([
                'product_id' => $request->product_id,
                'location_id' => $locationId,
                'quantity' => $request->quantity,
                'type' => $request->type,
                'reason' => $request->reason,
                'date' => $request->date
            ]);

            // Update quantity
            $stock = ProductLocationStock::firstOrCreate([
                'product_id' => $request->product_id,
                'location_id' => $locationId
            ], ['quantity' => 0]);

            if ($request->type === 'addition') {
                $stock->increment('quantity', $request->quantity);
            } else {
                $stock->decrement('quantity', $request->quantity);
            }

            DB::commit();
            return redirect()->route('stocks.adjustment')->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stocks.adjustment')->with('error', 'Error adjusting stock: ' . $e->getMessage());
        }
    }

    public function adjustDestroy($id)
    {
        DB::beginTransaction();
        try {
            $adj = StockAdjustment::findOrFail($id);
            if ($adj->location_id !== auth()->user()->location_id) {
                abort(403, 'Unauthorized access.');
            }
            $locationId = $adj->location_id;

            // Revert stock quantity changes
            $stock = ProductLocationStock::where('product_id', $adj->product_id)
                ->where('location_id', $locationId)
                ->first();

            if ($stock) {
                if ($adj->type === 'addition') {
                    $stock->decrement('quantity', $adj->quantity);
                } else {
                    $stock->increment('quantity', $adj->quantity);
                }
            }

            $adj->delete();

            DB::commit();
            return redirect()->route('stocks.adjustment')->with('success', 'Stock adjustment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stocks.adjustment')->with('error', 'Error deleting stock adjustment: ' . $e->getMessage());
        }
    }
}
