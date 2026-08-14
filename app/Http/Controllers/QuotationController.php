<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('location_id', auth()->user()->location_id)
            ->with(['customer', 'user'])
            ->orderBy('id', 'desc')
            ->get();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $locationId = auth()->user()->location_id;
        $products = Product::where('is_active', true)->with(['stocks' => function($q) use ($locationId) {
            $q->where('location_id', $locationId);
        }])->get();
        $customers = Account::customers()->get();

        return view('quotations.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'date' => 'required|date',
            'valid_till' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'delivery_charges' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $locationId = auth()->user()->location_id;

            $quotation = Quotation::create([
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'address' => $request->address,
                'total_amount' => 0,
                'discount' => $request->discount ?? 0,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'net_amount' => 0,
                'note' => $request->note,
                'date' => $request->date,
                'valid_till' => $request->valid_till,
                'status' => 'pending'
            ]);

            $totalAmount = 0;

            foreach ($request->products as $prod) {
                $subtotal = $prod['quantity'] * $prod['unit_price'];
                $totalAmount += $subtotal;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $prod['id'],
                    'quantity' => $prod['quantity'],
                    'unit_price' => $prod['unit_price'],
                    'subtotal' => $subtotal
                ]);
            }

            $netAmount = ($totalAmount - ($request->discount ?? 0)) + ($request->delivery_charges ?? 0);

            $quotation->update([
                'total_amount' => $totalAmount,
                'net_amount' => $netAmount
            ]);

            DB::commit();
            return response()->json(['success' => true, 'quotation_id' => $quotation->id, 'message' => 'Quotation created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show(Quotation $quotation)
    {
        if ($quotation->location_id !== auth()->user()->location_id) {
            abort(403);
        }
        $quotation->load(['customer', 'user', 'items.product']);
        return view('quotations.show', compact('quotation'));
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->location_id !== auth()->user()->location_id) {
            return redirect()->route('quotations.index')->with('error', 'Unauthorized action.');
        }

        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
