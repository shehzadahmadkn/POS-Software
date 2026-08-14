<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductLocationStock;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Sale::with(['customer', 'user'])->where('location_id', auth()->user()->location_id);
            
            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
            }
            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
            }
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('invoice_no', function ($row) {
                    return 'INV-' . str_pad($row->id, 5, '0', STR_PAD_LEFT);
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? 'Walk-in Customer';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->editColumn('net_amount', function ($row) {
                    return number_format($row->net_amount, 2);
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="' . route('sales.show', $row->id) . '" class="dropdown-item view-sale-btn"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>';
                    if (auth()->user()->can('edit-sale')) {
                        $html .= '<li><a href="' . route('sales.edit', $row->id) . '" class="dropdown-item edit-sale-btn"><i class="ri-pencil-fill align-bottom me-2 text-info"></i> Edit</a></li>';
                    }
                    $html .= '<li><a href="' . route('sales.activity', $row->id) . '" class="dropdown-item view-activity-btn"><i class="ri-history-line align-bottom me-2 text-warning"></i> View Activity</a></li>
                            <li><hr class="dropdown-divider"></li>';
                    if (auth()->user()->can('delete-sale')) {
                        $html .= '<li>
                            <form action="' . route('sales.destroy', $row->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure you want to delete and reverse this sale?\')">
                                    <i class="ri-delete-bin-fill align-bottom me-2"></i> Delete
                                </button>
                            </form>
                        </li>';
                    }
                    $html .= '</ul></div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('sales.index');
    }

    public function create()
    {
        $locationId = auth()->user()->location_id;
        $products = Product::where('is_active', true)->with(['stocks' => function($q) use ($locationId) {
            $q->where('location_id', $locationId);
        }])->get();
        
        $customers = Account::customers()->get();
        $groupAccounts = \App\Models\GroupAccount::with('customer')->get();
        $accounts = Account::business()->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('sales.pos', compact('products', 'customers', 'groupAccounts', 'accounts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
            'delivery_charges' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.warehouse_id' => 'required|exists:warehouses,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $locationId = auth()->user()->location_id;
            
            $sale = Sale::create([
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'total_amount' => 0,
                'discount' => $request->discount ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'account_id' => $request->account_id,
                'transaction_date' => $request->transaction_date,
                'payment_status' => $request->payment_status,
                'note' => $request->note,
                'net_amount' => 0,
                'paid_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($request->products as $prod) {
                $subtotal = $prod['quantity'] * $prod['unit_price'];
                $totalAmount += $subtotal;
                $productModel = Product::find($prod['id']);
                $costPrice = $productModel ? $productModel->cost_price : 0;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prod['id'],
                    'warehouse_id' => $prod['warehouse_id'],
                    'quantity' => $prod['quantity'],
                    'unit_price' => $prod['unit_price'],
                    'cost_price' => $costPrice,
                    'subtotal' => $subtotal,
                ]);

                $stock = ProductLocationStock::where('product_id', $prod['id'])
                    ->where('location_id', $locationId)
                    ->where('warehouse_id', $prod['warehouse_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->quantity < $prod['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: " . $prod['id']);
                }

                $stock->decrement('quantity', $prod['quantity']);
            }

            $netAmount = ($totalAmount - ($request->discount ?? 0)) + ($request->delivery_charges ?? 0);
            
            $paidAmount = 0;
            if ($request->payment_status === 'paid') {
                $paidAmount = $netAmount;
            } elseif ($request->payment_status === 'partial') {
                $paidAmount = floatval($request->paid_amount ?? 0);
            } elseif ($request->payment_status === 'pending') {
                $paidAmount = 0;
            } else {
                $paidAmount = floatval($request->paid_amount ?? $netAmount);
            }

            $sale->total_amount = $totalAmount;
            $sale->net_amount = $netAmount;
            $sale->paid_amount = $paidAmount;
            $sale->saveQuietly();

            // 1. Update customer balance (Debit for netAmount)
            if ($request->customer_id) {
                $customer = Account::find($request->customer_id);
                if ($customer) {
                    $customer->increment('balance', $netAmount);
                }
            }

            // 2. Process Payment
            if ($request->account_id && $paidAmount > 0) {
                $account = Account::find($request->account_id);
                if ($account) {
                    $account->increment('balance', $paidAmount);
                }

                if ($request->customer_id) {
                    $customer = Account::find($request->customer_id);
                    if ($customer) {
                        $customer->decrement('balance', $paidAmount);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'sale_id' => $sale->id, 'message' => 'Sale completed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show(Sale $sale)
    {
        if ($sale->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $sale->load(['items.product']);
        $locationId = auth()->user()->location_id;
        $products = Product::where('is_active', true)->with(['stocks' => function($q) use ($locationId) {
            $q->where('location_id', $locationId);
        }])->get();
        $customers = Account::customers()->get();
        $groupAccounts = \App\Models\GroupAccount::with('customer')->get();
        $accounts = Account::business()->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('sales.pos', compact('sale', 'products', 'customers', 'groupAccounts', 'accounts', 'warehouses'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $request->validate([
            'customer_id' => 'nullable|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
            'delivery_charges' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.warehouse_id' => 'required|exists:warehouses,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $locationId = auth()->user()->location_id;

            // 1. Reverse old stock subtraction
            foreach ($sale->items as $item) {
                $stock = ProductLocationStock::where('product_id', $item->product_id)
                    ->where('location_id', $sale->location_id)
                    ->where('warehouse_id', $item->warehouse_id)
                    ->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // 2. Reverse old customer and account balances
            if ($sale->customer_id) {
                $customer = Account::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('balance', $sale->net_amount);
                }
            }
            if ($sale->account_id && $sale->paid_amount > 0) {
                $account = Account::find($sale->account_id);
                if ($account) {
                    $account->decrement('balance', $sale->paid_amount);
                }
                if ($sale->customer_id) {
                    $customer = Account::find($sale->customer_id);
                    if ($customer) {
                        $customer->increment('balance', $sale->paid_amount);
                    }
                }
            }

            // Delete old sales items
            $sale->items()->delete();

            // 3. Process new products
            $totalAmount = 0;
            foreach ($request->products as $prod) {
                $subtotal = $prod['quantity'] * $prod['unit_price'];
                $totalAmount += $subtotal;
                $productModel = Product::find($prod['id']);
                $costPrice = $productModel ? $productModel->cost_price : 0;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prod['id'],
                    'warehouse_id' => $prod['warehouse_id'],
                    'quantity' => $prod['quantity'],
                    'unit_price' => $prod['unit_price'],
                    'cost_price' => $costPrice,
                    'subtotal' => $subtotal,
                ]);

                $stock = ProductLocationStock::where('product_id', $prod['id'])
                    ->where('location_id', $locationId)
                    ->where('warehouse_id', $prod['warehouse_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->quantity < $prod['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: " . $prod['id']);
                }

                $stock->decrement('quantity', $prod['quantity']);
            }

            $netAmount = ($totalAmount - ($request->discount ?? 0)) + ($request->delivery_charges ?? 0);
            
            $paidAmount = 0;
            if ($request->payment_status === 'paid') {
                $paidAmount = $netAmount;
            } elseif ($request->payment_status === 'partial') {
                $paidAmount = floatval($request->paid_amount ?? 0);
            } elseif ($request->payment_status === 'pending') {
                $paidAmount = 0;
            } else {
                $paidAmount = floatval($request->paid_amount ?? $netAmount);
            }

            $sale->update([
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'discount' => $request->discount ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'account_id' => $request->account_id,
                'transaction_date' => $request->transaction_date,
                'payment_status' => $request->payment_status,
                'note' => $request->note,
                'net_amount' => $netAmount,
                'paid_amount' => $paidAmount,
            ]);

            // Apply new customer balance
            if ($request->customer_id) {
                $customer = Account::find($request->customer_id);
                if ($customer) {
                    $customer->increment('balance', $netAmount);
                }
            }

            // Apply new account deposit
            if ($request->account_id && $paidAmount > 0) {
                $account = Account::find($request->account_id);
                if ($account) {
                    $account->increment('balance', $paidAmount);
                }
                if ($request->customer_id) {
                    $customer = Account::find($request->customer_id);
                    if ($customer) {
                        $customer->decrement('balance', $paidAmount);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'sale_id' => $sale->id, 'message' => 'Sale updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Sale $sale)
    {
        DB::beginTransaction();
        try {
            if ($sale->location_id !== auth()->user()->location_id) {
                throw new \Exception("Unauthorized action.");
            }

            // Reverse stock
            foreach ($sale->items as $item) {
                $stock = ProductLocationStock::where('product_id', $item->product_id)
                    ->where('location_id', $sale->location_id)
                    ->where('warehouse_id', $item->warehouse_id)
                    ->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // Reverse customer and account balances
            if ($sale->customer_id) {
                $customer = Account::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('balance', $sale->net_amount);
                }
            }
            if ($sale->account_id && $sale->paid_amount > 0) {
                $account = Account::find($sale->account_id);
                if ($account) {
                    $account->decrement('balance', $sale->paid_amount);
                }
                if ($sale->customer_id) {
                    $customer = Account::find($sale->customer_id);
                    if ($customer) {
                        $customer->increment('balance', $sale->paid_amount);
                    }
                }
            }

            // Delete items and sale
            $sale->items()->delete();
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale deleted and reversed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }

    public function activity(Sale $sale)
    {
        $activities = $sale->activities()->with('causer')->latest()->get();
        return view('sales.activity', compact('sale', 'activities'));
    }
}
