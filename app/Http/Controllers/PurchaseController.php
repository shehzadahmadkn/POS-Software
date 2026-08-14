<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;

use App\Models\ProductLocationStock;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Purchase::with(['vendor', 'user'])->where('location_id', auth()->user()->location_id);
            
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('invoice_no', function ($row) {
                    return 'PUR-' . str_pad($row->id, 5, '0', STR_PAD_LEFT);
                })
                ->addColumn('vendor_name', function ($row) {
                    return $row->vendor->name ?? 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->editColumn('net_amount', function ($row) {
                    return number_format($row->net_amount > 0 ? $row->net_amount : $row->total_amount, 2);
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="' . route('purchases.show', $row->id) . '" class="dropdown-item view-purchase-btn"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>';
                    if (auth()->user()->can('edit-purchase')) {
                        $html .= '<li><a href="' . route('purchases.edit', $row->id) . '" class="dropdown-item edit-purchase-btn"><i class="ri-pencil-fill align-bottom me-2 text-info"></i> Edit</a></li>';
                    }
                    $html .= '<li><a href="' . route('purchases.activity', $row->id) . '" class="dropdown-item view-activity-btn"><i class="ri-history-line align-bottom me-2 text-warning"></i> View Activity</a></li>
                            <li><hr class="dropdown-divider"></li>';
                    if (auth()->user()->can('delete-purchase')) {
                        $html .= '<li>
                            <form action="' . route('purchases.destroy', $row->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure you want to delete and reverse this purchase?\')">
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
        
        return view('purchases.index');
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $vendors = Account::vendors()->get();
        $groupAccounts = \App\Models\GroupAccount::with('vendor')->get();
        $accounts = Account::business()->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('purchases.create', compact('products', 'vendors', 'groupAccounts', 'accounts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:accounts,id',
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
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $locationId = auth()->user()->location_id;
            
            $purchase = Purchase::create([
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'vendor_id' => $request->vendor_id,
                'total_amount' => 0,
                'discount' => $request->discount ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'account_id' => $request->account_id,
                'transaction_date' => $request->transaction_date,
                'payment_status' => $request->payment_status,
                'note' => $request->note,
            ]);

            $totalAmount = 0;

            foreach ($request->products as $prod) {
                $subtotal = $prod['quantity'] * $prod['unit_price'];
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $prod['id'],
                    'warehouse_id' => $prod['warehouse_id'],
                    'quantity' => $prod['quantity'],
                    'unit_price' => $prod['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Update product pricing (cost and selling)
                Product::where('id', $prod['id'])->update([
                    'cost_price' => $prod['unit_price'],
                    'selling_price' => $prod['selling_price']
                ]);

                // Update stock
                $stock = ProductLocationStock::firstOrCreate([
                    'product_id' => $prod['id'],
                    'location_id' => $locationId,
                    'warehouse_id' => $prod['warehouse_id'],
                ], ['quantity' => 0]);

                $stock->increment('quantity', $prod['quantity']);
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

            $purchase->total_amount = $totalAmount;
            $purchase->discount = $request->discount ?? 0;
            $purchase->net_amount = $netAmount;
            $purchase->paid_amount = $paidAmount;
            $purchase->saveQuietly();

            // Update vendor balance (Credit for netAmount)
            if ($request->vendor_id) {
                $vendor = Account::find($request->vendor_id);
                if ($vendor) {
                    $vendor->increment('balance', $netAmount);
                }
            }

            // Deduct from account and process payment
            if ($request->account_id && $paidAmount > 0) {
                $account = Account::find($request->account_id);
                if ($account) {
                    $account->decrement('balance', $paidAmount);
                }
                
                if ($request->vendor_id) {
                    $vendor = Account::find($request->vendor_id);
                    if ($vendor) {
                        $vendor->decrement('balance', $paidAmount);
                    }
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase completed successfully',
                    'purchase_id' => $purchase->id
                ]);
            }
            return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Error recording purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        if ($purchase->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $purchase->load(['vendor', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $purchase->load(['items.product']);
        $products = Product::where('is_active', true)->get();
        $vendors = Account::vendors()->get();
        $groupAccounts = \App\Models\GroupAccount::with('vendor')->get();
        $accounts = Account::business()->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('purchases.create', compact('purchase', 'products', 'vendors', 'groupAccounts', 'accounts', 'warehouses'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->location_id !== auth()->user()->location_id) {
            abort(403, 'Unauthorized access.');
        }
        $request->validate([
            'vendor_id' => 'nullable|exists:accounts,id',
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
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $locationId = auth()->user()->location_id;

            // 1. Reverse old stock addition
            foreach ($purchase->items as $item) {
                $stock = ProductLocationStock::where('product_id', $item->product_id)
                    ->where('location_id', $purchase->location_id)
                    ->where('warehouse_id', $item->warehouse_id)
                    ->first();
                if ($stock) {
                    $stock->decrement('quantity', $item->quantity);
                }
            }

            // 2. Reverse old account balance payment (add back money spent)
            if ($purchase->vendor_id) {
                $vendor = Account::find($purchase->vendor_id);
                if ($vendor) {
                    $vendor->decrement('balance', $purchase->net_amount);
                }
            }
            
            $oldPaidAmount = $purchase->paid_amount ?? 0;
            if ($purchase->account_id && $oldPaidAmount > 0) {
                $account = Account::find($purchase->account_id);
                if ($account) {
                    $account->increment('balance', $oldPaidAmount);
                }
                if ($purchase->vendor_id) {
                    $vendor = Account::find($purchase->vendor_id);
                    if ($vendor) {
                        $vendor->increment('balance', $oldPaidAmount);
                    }
                }
            }

            // Delete old purchase items
            $purchase->items()->delete();

            // 3. Process new products
            $totalAmount = 0;
            foreach ($request->products as $prod) {
                $subtotal = $prod['quantity'] * $prod['unit_price'];
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $prod['id'],
                    'warehouse_id' => $prod['warehouse_id'],
                    'quantity' => $prod['quantity'],
                    'unit_price' => $prod['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Update product pricing (cost and selling)
                Product::where('id', $prod['id'])->update([
                    'cost_price' => $prod['unit_price'],
                    'selling_price' => $prod['selling_price']
                ]);

                // Update stock
                $stock = ProductLocationStock::firstOrCreate([
                    'product_id' => $prod['id'],
                    'location_id' => $locationId,
                    'warehouse_id' => $prod['warehouse_id'],
                ], ['quantity' => 0]);

                $stock->increment('quantity', $prod['quantity']);
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

            $purchase->update([
                'vendor_id' => $request->vendor_id,
                'total_amount' => $totalAmount,
                'discount' => $request->discount ?? 0,
                'net_amount' => $netAmount,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'account_id' => $request->account_id,
                'transaction_date' => $request->transaction_date,
                'payment_status' => $request->payment_status,
                'note' => $request->note,
                'paid_amount' => $paidAmount,
            ]);

            // Apply new vendor balance
            if ($request->vendor_id) {
                $vendor = Account::find($request->vendor_id);
                if ($vendor) {
                    $vendor->increment('balance', $netAmount);
                }
            }

            // Deduct new payment amount from account
            if ($request->account_id && $paidAmount > 0) {
                $account = Account::find($request->account_id);
                if ($account) {
                    $account->decrement('balance', $paidAmount);
                }
                if ($request->vendor_id) {
                    $vendor = Account::find($request->vendor_id);
                    if ($vendor) {
                        $vendor->decrement('balance', $paidAmount);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'purchase_id' => $purchase->id, 'message' => 'Purchase updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            if ($purchase->location_id !== auth()->user()->location_id) {
                throw new \Exception("Unauthorized action.");
            }

            // Reverse stock
            foreach ($purchase->items as $item) {
                $stock = ProductLocationStock::where('product_id', $item->product_id)
                    ->where('location_id', $purchase->location_id)
                    ->where('warehouse_id', $item->warehouse_id)
                    ->first();
                if ($stock) {
                    $stock->decrement('quantity', $item->quantity);
                }
            }

            // Reverse vendor balance
            if ($purchase->vendor_id) {
                $vendor = Account::find($purchase->vendor_id);
                if ($vendor) {
                    $vendor->decrement('balance', $purchase->net_amount);
                }
            }

            // Reverse account balance (add back the money we spent)
            $paidAmount = $purchase->paid_amount ?? 0;
            if ($purchase->account_id && $paidAmount > 0) {
                $account = Account::find($purchase->account_id);
                if ($account) {
                    $account->increment('balance', $paidAmount);
                }
                if ($purchase->vendor_id) {
                    $vendor = Account::find($purchase->vendor_id);
                    if ($vendor) {
                        $vendor->increment('balance', $paidAmount);
                    }
                }
            }

            // Delete items and purchase
            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted and reversed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.index')->with('error', 'Error deleting purchase: ' . $e->getMessage());
        }
    }

    // Edit and Update are typically restricted for accounting integrity in a POS,
    // so we'll skip them for now or implement them only with reverse-stock logic.

    public function activity(Purchase $purchase)
    {
        $activities = $purchase->activities()->with('causer')->latest()->get();
        return view('purchases.activity', compact('purchase', 'activities'));
    }
}
