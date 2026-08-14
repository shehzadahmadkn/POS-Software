<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions', 'users')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::get();
        $groupedPermissions = $this->getGroupedPermissions($permissions);
        return view('roles.create', compact('permissions', 'groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        return redirect()->route('roles.index')->with('success','Role created successfully');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::get();
        $groupedPermissions = $this->getGroupedPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('name', 'name')->all();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'groupedPermissions'));
    }

    private function getGroupedPermissions($permissions)
    {
        $map = [
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

        $grouped = [];
        foreach ($permissions as $permission) {
            $groupName = $map[$permission->name] ?? 'Other';
            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }
            $grouped[$groupName][] = $permission;
        }
        
        return $grouped;
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permission' => 'required|array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        return redirect()->route('roles.index')->with('success','Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success','Role deleted successfully');
    }
}
