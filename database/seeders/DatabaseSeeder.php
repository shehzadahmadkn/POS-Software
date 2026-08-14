<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductLocationStock;
use App\Models\GroupAccount;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\PaymentReceive;
use App\Models\StockAdjustment;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('quotation_items')->truncate();
        DB::table('quotations')->truncate();
        DB::table('stock_adjustments')->truncate();
        DB::table('payment_receives')->truncate();
        DB::table('expense_categories')->truncate();
        DB::table('expenses')->truncate();
        DB::table('transfers')->truncate();
        DB::table('transactions')->truncate();
        DB::table('purchase_items')->truncate();
        DB::table('purchases')->truncate();
        DB::table('sale_items')->truncate();
        DB::table('sales')->truncate();
        DB::table('group_accounts')->truncate();
        DB::table('product_location_stocks')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('accounts')->truncate();
        DB::table('users')->truncate();
        DB::table('locations')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Location
        $location = Location::create([
            'name' => 'Main Warehouse & Retail',
            'address' => 'G-11 Markaz, Islamabad',
            'is_active' => true,
        ]);

        // 3. Roles and Users
        $adminRole = Role::create(['name' => 'Admin']);
        $cashierRole = Role::create(['name' => 'Cashier']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'location_id' => $location->id,
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);

        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password'),
            'location_id' => $location->id,
            'is_active' => true,
        ]);
        $cashier->assignRole($cashierRole);

        // 3.5. Warehouse
        $mainWarehouse = \App\Models\Warehouse::create([
            'name' => 'Main Warehouse',
            'address' => 'G-11 Markaz, Islamabad',
            'status' => 'active',
        ]);

        // 4. Business Accounts
        $cashAcc = Account::create([
            'name' => 'Cash Account',
            'type' => 'business',
            'sub_type' => 'cash',
            'balance' => 100000.00,
        ]);

        $bankAcc = Account::create([
            'name' => 'Meezan Bank Ltd',
            'type' => 'business',
            'sub_type' => 'bank',
            'balance' => 250000.00,
        ]);

        $chequeAcc = Account::create([
            'name' => 'HBL Cheques',
            'type' => 'business',
            'sub_type' => 'cheque',
            'balance' => 50000.00,
        ]);

        // 5. Product Categories
        $electronics = Category::create(['name' => 'Electronics']);
        $beverages = Category::create(['name' => 'Beverages']);
        $snacks = Category::create(['name' => 'Snacks']);

        // 6. Products
        $iphone = Product::create([
            'name' => 'iPhone 15 Pro',
            'sku' => 'IPH-15P',
            'barcode' => '190199123456',
            'description' => 'Apple iPhone 15 Pro 256GB Black Titanium',
            'cost_price' => 200000.00,
            'selling_price' => 240000.00,
            'category_id' => $electronics->id,
            'is_active' => true
        ]);

        $coke = Product::create([
            'name' => 'Coca-Cola 1.5L',
            'sku' => 'COKE-1.5',
            'barcode' => '5449000000996',
            'description' => 'Original Taste Coca-Cola Soft Drink',
            'cost_price' => 120.00,
            'selling_price' => 150.00,
            'category_id' => $beverages->id,
            'is_active' => true
        ]);

        $lays = Product::create([
            'name' => 'Lays Masala Large',
            'sku' => 'LAYS-M',
            'barcode' => '896400001234',
            'description' => 'Lays Masala Flavored Potato Chips',
            'cost_price' => 80.00,
            'selling_price' => 100.00,
            'category_id' => $snacks->id,
            'is_active' => true
        ]);

        // 7. Initial Stocks
        $stockIphone = ProductLocationStock::create([
            'product_id' => $iphone->id,
            'location_id' => $location->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 20
        ]);

        $stockCoke = ProductLocationStock::create([
            'product_id' => $coke->id,
            'location_id' => $location->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 100
        ]);

        $stockLays = ProductLocationStock::create([
            'product_id' => $lays->id,
            'location_id' => $location->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 150
        ]);

        // 8. Customers & Vendors
        $sarah = Account::create([
            'name' => 'Sarah Khan',
            'type' => 'customer',
            'phone' => '03001234567',
            'address' => 'F-8/2, Islamabad',
            'balance' => 0.00
        ]);

        $zain = Account::create([
            'name' => 'Zain Ahmed',
            'type' => 'customer',
            'phone' => '03129876543',
            'address' => 'G-9/1, Islamabad',
            'balance' => 0.00
        ]);

        $appleDist = Account::create([
            'name' => 'Apple Distribution North',
            'type' => 'vendor',
            'phone' => '02133221100',
            'address' => 'Korangi Industrial Area, Karachi',
            'balance' => 0.00
        ]);

        $cokeBottlers = Account::create([
            'name' => 'Coke Beverages Pakistan',
            'type' => 'vendor',
            'phone' => '04235554433',
            'address' => 'Gulberg III, Lahore',
            'balance' => 0.00
        ]);

        // 9. Group Account
        $groupAcc = GroupAccount::create([
            'name' => 'Sarah Tech Hub Group',
            'customer_id' => $sarah->id,
            'vendor_id' => $appleDist->id
        ]);

        // 10. Deposits/Withdrawals
        Transaction::create([
            'reference_no' => 'TRX-001',
            'account_id' => $cashAcc->id,
            'type' => 'withdrawal',
            'amount' => 10000.00,
            'transaction_date' => date('Y-m-d'),
            'note' => 'Petty Cash withdrawal for stationery'
        ]);
        $cashAcc->decrement('balance', 10000.00);

        // 11. Sales (Invoice)
        $sale1 = Sale::create([
            'location_id' => $location->id,
            'user_id' => $admin->id,
            'customer_id' => null,
            'total_amount' => 500.00, // (2 Coke * 150) + (2 Lays * 100)
            'discount' => 50.00,
            'delivery_charges' => 0,
            'account_id' => $cashAcc->id,
            'transaction_date' => date('Y-m-d'),
            'payment_status' => 'paid',
            'net_amount' => 450.00,
            'paid_amount' => 450.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $coke->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 2,
            'unit_price' => 150.00,
            'cost_price' => 120.00,
            'subtotal' => 300.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $lays->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'cost_price' => 80.00,
            'subtotal' => 200.00,
        ]);
        $stockCoke->decrement('quantity', 2);
        $stockLays->decrement('quantity', 2);
        $cashAcc->increment('balance', 450.00);

        $sale2 = Sale::create([
            'location_id' => $location->id,
            'user_id' => $admin->id,
            'customer_id' => $zain->id,
            'total_amount' => 240000.00,
            'discount' => 0.00,
            'delivery_charges' => 0,
            'account_id' => $bankAcc->id,
            'transaction_date' => date('Y-m-d'),
            'payment_status' => 'partial',
            'net_amount' => 240000.00,
            'paid_amount' => 100000.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $iphone->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 1,
            'unit_price' => 240000.00,
            'cost_price' => 200000.00,
            'subtotal' => 240000.00,
        ]);
        $stockIphone->decrement('quantity', 1);
        $bankAcc->increment('balance', 100000.00);
        $zain->increment('balance', 140000.00); 

        // 12. Purchases
        $purchase1 = Purchase::create([
            'location_id' => $location->id,
            'user_id' => $admin->id,
            'vendor_id' => $appleDist->id,
            'total_amount' => 1000000.00,
            'delivery_charges' => 0,
            'account_id' => $bankAcc->id,
            'transaction_date' => date('Y-m-d'),
            'payment_status' => 'partial',
            'paid_amount' => 200000.00,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'product_id' => $iphone->id,
            'warehouse_id' => $mainWarehouse->id,
            'quantity' => 5,
            'unit_price' => 200000.00,
            'subtotal' => 1000000.00
        ]);
        $stockIphone->increment('quantity', 5);
        $bankAcc->decrement('balance', 200000.00);
        $appleDist->increment('balance', 800000.00); 

        // 13. Transfers
        Transfer::create([
            'reference_no' => 'TR-001',
            'from_account_id' => $bankAcc->id,
            'to_account_id' => $cashAcc->id,
            'amount' => 50000.00,
            'date' => date('Y-m-d'),
            'note' => 'Move cash to store safe'
        ]);
        $bankAcc->decrement('balance', 50000.00);
        $cashAcc->increment('balance', 50000.00);

        // 14. Expense
        $rentCategory = ExpenseCategory::create(['name' => 'Office Rent']);
        $utilCategory = ExpenseCategory::create(['name' => 'Electricity Bills']);
        
        Expense::create([
            'reference_no' => 'EXP-001',
            'expense_category_id' => $rentCategory->id,
            'account_id' => $bankAcc->id,
            'amount' => 30000.00,
            'date' => date('Y-m-d'),
            'note' => 'Monthly rental payment',
            'location_id' => $location->id,
            'user_id' => $admin->id
        ]);
        $bankAcc->decrement('balance', 30000.00);

        // 15. Payment Receive
        PaymentReceive::create([
            'reference_no' => 'PR-001',
            'from_account_id' => $zain->id,
            'account_id' => $cashAcc->id,
            'amount' => 40000.00,
            'date' => date('Y-m-d'),
            'note' => 'Zain Partial payment'
        ]);
        $zain->decrement('balance', 40000.00);
        $cashAcc->increment('balance', 40000.00);

        // 16. Stock Adjustment
        StockAdjustment::create([
            'product_id' => $coke->id,
            'location_id' => $location->id,
            'quantity' => 2,
            'type' => 'subtraction',
            'reason' => 'Expired stock write off',
            'date' => date('Y-m-d')
        ]);
        $stockCoke->decrement('quantity', 2);

        // 17. Quotations
        $quotation = Quotation::create([
            'location_id' => $location->id,
            'user_id' => $admin->id,
            'customer_id' => $sarah->id,
            'total_amount' => 480000.00,
            'discount' => 10000.00,
            'delivery_charges' => 2000.00,
            'net_amount' => 472000.00,
            'note' => 'Valid for 15 days',
            'date' => date('Y-m-d'),
            'status' => 'pending'
        ]);
        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $iphone->id,
            'quantity' => 2,
            'unit_price' => 240000.00,
            'subtotal' => 480000.00
        ]);
    }
}
