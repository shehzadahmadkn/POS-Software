<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NavigationPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-dashboard' => 'Dashboard',
            'create-sale' => 'Sale',
            'view-sales-history' => 'Sale',
            'edit-sale' => 'Sale',
            'delete-sale' => 'Sale',
            'create-purchase' => 'Purchase',
            'view-purchase-history' => 'Purchase',
            'edit-purchase' => 'Purchase',
            'delete-purchase' => 'Purchase',
            'create-quotation' => 'Quotation',
            'view-quotation-history' => 'Quotation',
            'edit-quotation' => 'Quotation',
            'delete-quotation' => 'Quotation',
            'view-stock' => 'Stocks',
            'view-stock-zero' => 'Stocks',
            'view-stock-above-zero' => 'Stocks',
            'view-stock-below-zero' => 'Stocks',
            'manage-stock-adjustment' => 'Stocks',
            'manage-stock-transfers' => 'Stocks',
            'edit-stock-transfer' => 'Stocks',
            'delete-stock-transfer' => 'Stocks',
            'view-product-list' => 'Products',
            'manage-product-categories' => 'Products',
            'edit-product' => 'Products',
            'delete-product' => 'Products',
            'edit-category' => 'Products',
            'delete-category' => 'Products',
            'create-account' => 'Finance',
            'view-business-accounts' => 'Finance',
            'view-customer-accounts' => 'Finance',
            'view-vendor-accounts' => 'Finance',
            'view-group-accounts' => 'Finance',
            'manage-payment-receiving' => 'Finance',
            'manage-deposit-withdrawal' => 'Finance',
            'manage-transfer' => 'Finance',
            'manage-expenses' => 'Finance',
            'edit-customer' => 'Finance',
            'delete-customer' => 'Finance',
            'edit-vendor' => 'Finance',
            'delete-vendor' => 'Finance',
            'edit-expense' => 'Finance',
            'delete-expense' => 'Finance',
            'view-profit-loss' => 'Reports',
            'view-daily-cash-book' => 'Reports',
            'view-product-wise-sales' => 'Reports',
            'view-ledger-report' => 'Reports',
            'view-stock-report' => 'Reports',
            'view-customer-wise-sales' => 'Reports',
            'manage-warehouses' => 'Warehouses',
            'edit-warehouse' => 'Warehouses',
            'delete-warehouse' => 'Warehouses',
            'manage-todos' => 'Todo List',
            'edit-todo' => 'Todo List',
            'delete-todo' => 'Todo List',
            'manage-users' => 'Settings',
            'edit-user' => 'Settings',
            'delete-user' => 'Settings',
            'manage-roles' => 'Settings',
            'edit-role' => 'Settings',
            'delete-role' => 'Settings',
            'view-activity-logs' => 'Settings',
        ];

        foreach ($permissions as $permission => $group) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ], [
                // We'll store the group name in a custom attribute, or just rely on Controller grouping
            ]);
        }
        
        // Ensure Admin role has all these permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(array_keys($permissions));
    }
}
