<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                @if(\App\Models\Setting::getSetting('logo'))
                    <img src="{{ asset(\App\Models\Setting::getSetting('logo')) }}" alt="" height="22">
                @elseif(\App\Models\Setting::getSetting('logo_text'))
                    <h5 class="text-white mt-3">{{ \App\Models\Setting::getSetting('logo_text') }}</h5>
                @else
                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                @endif
            </span>
            <span class="logo-lg">
                @if(\App\Models\Setting::getSetting('logo'))
                    <img src="{{ asset(\App\Models\Setting::getSetting('logo')) }}" alt="" height="17">
                @elseif(\App\Models\Setting::getSetting('logo_text'))
                    <h3 class="text-white mt-3">{{ \App\Models\Setting::getSetting('logo_text') }}</h3>
                @else
                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
                @endif
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                @if(\App\Models\Setting::getSetting('logo'))
                    <img src="{{ asset(\App\Models\Setting::getSetting('logo')) }}" alt="" height="22">
                @elseif(\App\Models\Setting::getSetting('logo_text'))
                    <h5 class="text-white mt-3">{{ \App\Models\Setting::getSetting('logo_text') }}</h5>
                @else
                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                @endif
            </span>
            <span class="logo-lg">
                @if(\App\Models\Setting::getSetting('logo'))
                    <img src="{{ asset(\App\Models\Setting::getSetting('logo')) }}" alt="" height="17">
                @elseif(\App\Models\Setting::getSetting('logo_text'))
                    <h3 class="text-white mt-3">{{ \App\Models\Setting::getSetting('logo_text') }}</h3>
                @else
                    <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
                @endif
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                @can('view-dashboard')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboard</span>
                    </a>
                </li> <!-- end Dashboard Menu -->
                @endcan

                @canany(['create-sale', 'view-sales-history'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarSale" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSale">
                        <i class="ri-shopping-cart-line"></i> <span data-key="t-sale">Sale</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarSale">
                        <ul class="nav nav-sm flex-column">
                            @can('create-sale')
                            <li class="nav-item">
                                <a href="{{ route('sales.create') }}" class="nav-link" data-key="t-create-sale">Create a Sale</a>
                            </li>
                            @endcan
                            @can('view-sales-history')
                            <li class="nav-item">
                                <a href="{{ route('sales.index') }}" class="nav-link" data-key="t-sales-history">Sales History</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['create-purchase', 'view-purchase-history'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPurchase" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPurchase">
                        <i class="ri-truck-line"></i> <span data-key="t-purchase">Purchase</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPurchase">
                        <ul class="nav nav-sm flex-column">
                            @can('create-purchase')
                            <li class="nav-item">
                                <a href="{{ route('purchases.create') }}" class="nav-link" data-key="t-create-purchase">Create a Purchase</a>
                            </li>
                            @endcan
                            @can('view-purchase-history')
                            <li class="nav-item">
                                <a href="{{ route('purchases.index') }}" class="nav-link" data-key="t-purchase-history">Purchase History</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['create-quotation', 'view-quotation-history'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarQuotation" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarQuotation">
                        <i class="ri-file-text-line"></i> <span data-key="t-quotation">Quotation</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarQuotation">
                        <ul class="nav nav-sm flex-column">
                            @can('create-quotation')
                            <li class="nav-item">
                                <a href="{{ route('quotations.create') }}" class="nav-link" data-key="t-create-quotation">Create a Quotation</a>
                            </li>
                            @endcan
                            @can('view-quotation-history')
                            <li class="nav-item">
                                <a href="{{ route('quotations.index') }}" class="nav-link" data-key="t-quotation-history">Quotation History</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['view-stock', 'view-stock-zero', 'view-stock-above-zero', 'view-stock-below-zero', 'manage-stock-adjustment', 'manage-stock-transfers'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarStocks" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarStocks">
                        <i class="ri-inbox-archive-line"></i> <span data-key="t-stocks">Stocks</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarStocks">
                        <ul class="nav nav-sm flex-column">
                            @can('view-stock')
                            <li class="nav-item">
                                <a href="{{ route('stocks.index') }}" class="nav-link" data-key="t-stock">Stock</a>
                            </li>
                            @endcan
                            @can('view-stock-zero')
                            <li class="nav-item">
                                <a href="{{ route('stocks.index', ['filter' => 'zero']) }}" class="nav-link" data-key="t-stock-zero">Stock with zero Qty</a>
                            </li>
                            @endcan
                            @can('view-stock-above-zero')
                            <li class="nav-item">
                                <a href="{{ route('stocks.index', ['filter' => 'above_zero']) }}" class="nav-link" data-key="t-stock-above-zero">Stock with above zero qty</a>
                            </li>
                            @endcan
                            @can('view-stock-below-zero')
                            <li class="nav-item">
                                <a href="{{ route('stocks.index', ['filter' => 'below_zero']) }}" class="nav-link" data-key="t-stock-below-zero">Stock with below zero qty</a>
                            </li>
                            @endcan
                            @can('manage-stock-adjustment')
                            <li class="nav-item">
                                <a href="{{ route('stocks.adjustment') }}" class="nav-link" data-key="t-stock-adjustment">Stock Adjustment</a>
                            </li>
                            @endcan
                            @can('manage-stock-transfers')
                            <li class="nav-item">
                                <a href="{{ route('stock-transfers.index') }}" class="nav-link" data-key="t-stock-transfers">Stock Transfers</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['view-product-list', 'manage-product-categories'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="ri-store-2-line"></i> <span data-key="t-products">Products</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            @can('view-product-list')
                            <li class="nav-item">
                                <a href="{{ route('products.index') }}" class="nav-link" data-key="t-product-list">Product List</a>
                            </li>
                            @endcan
                            @can('manage-product-categories')
                            <li class="nav-item">
                                <a href="{{ route('categories.index') }}" class="nav-link" data-key="t-product-categories">Product Categories</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['create-account', 'view-business-accounts', 'view-customer-accounts', 'view-vendor-accounts', 'view-group-accounts', 'manage-payment-receiving', 'manage-deposit-withdrawal', 'manage-transfer', 'manage-expenses'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarFinance" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarFinance">
                        <i class="ri-wallet-3-line"></i> <span data-key="t-finance">Finance</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarFinance">
                        <ul class="nav nav-sm flex-column">
                            @can('create-account')
                            <li class="nav-item">
                                <a href="{{ route('accounts.create') }}" class="nav-link" data-key="t-create-account">Create Account</a>
                            </li>
                            @endcan
                            @can('view-business-accounts')
                            <li class="nav-item">
                                <a href="{{ route('accounts.index') }}" class="nav-link" data-key="t-business-accounts">Business Accounts</a>
                            </li>
                            @endcan
                            @can('view-customer-accounts')
                            <li class="nav-item">
                                <a href="{{ route('customers.index') }}" class="nav-link" data-key="t-customer-accounts">Customer Accounts</a>
                            </li>
                            @endcan
                            @can('view-vendor-accounts')
                            <li class="nav-item">
                                <a href="{{ route('vendors.index') }}" class="nav-link" data-key="t-vendor-accounts">Vendor Accounts</a>
                            </li>
                            @endcan
                            @can('view-group-accounts')
                            <li class="nav-item">
                                <a href="{{ route('group_accounts.index') }}" class="nav-link" data-key="t-group-accounts">Group Accounts</a>
                            </li>
                            @endcan
                            @can('manage-payment-receiving')
                            <li class="nav-item">
                                <a href="{{ route('payment_receives.index') }}" class="nav-link" data-key="t-payment-receiving">Payment Receiving</a>
                            </li>
                            @endcan
                            @can('manage-deposit-withdrawal')
                            <li class="nav-item">
                                <a href="{{ route('transactions.index') }}" class="nav-link" data-key="t-deposit-withdrawal">Deposit / Withdrawal</a>
                            </li>
                            @endcan
                            @can('manage-transfer')
                            <li class="nav-item">
                                <a href="{{ route('transfers.index') }}" class="nav-link" data-key="t-transfer">Transfer</a>
                            </li>
                            @endcan
                            @can('manage-expenses')
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}" class="nav-link" data-key="t-expenses">Expenses</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @canany(['view-profit-loss', 'view-daily-cash-book', 'view-product-wise-sales', 'view-ledger-report', 'view-stock-report', 'view-customer-wise-sales'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReports">
                        <i class="ri-bar-chart-2-line"></i> <span data-key="t-reports">Reports</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarReports">
                        <ul class="nav nav-sm flex-column">
                            @can('view-profit-loss')
                            <li class="nav-item">
                                <a href="{{ route('reports.profit_loss') }}" class="nav-link" data-key="t-profit-loss">Profit / Loss</a>
                            </li>
                            @endcan
                            @can('view-daily-cash-book')
                            <li class="nav-item">
                                <a href="{{ route('reports.daily_cash_book') }}" class="nav-link" data-key="t-daily-cash-book">Daily Cash Book</a>
                            </li>
                            @endcan
                            @can('view-product-wise-sales')
                            <li class="nav-item">
                                <a href="{{ route('reports.product_wise_sales') }}" class="nav-link" data-key="t-product-wise-sales">Product Wise Sales</a>
                            </li>
                            @endcan
                            @can('view-ledger-report')
                            <li class="nav-item">
                                <a href="{{ route('reports.ledger_report') }}" class="nav-link" data-key="t-ledger-report">Ledger Report</a>
                            </li>
                            @endcan
                            @can('view-stock-report')
                            <li class="nav-item">
                                <a href="{{ route('reports.stock_report') }}" class="nav-link" data-key="t-stock-report">Stock Report</a>
                            </li>
                            @endcan
                            @can('view-customer-wise-sales')
                            <li class="nav-item">
                                <a href="{{ route('reports.customer_wise_sales') }}" class="nav-link" data-key="t-customer-wise-sales">Customer Wise Sales Report</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @can('manage-warehouses')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('warehouses.index') }}">
                        <i class="ri-building-4-line"></i> <span data-key="t-warehouses">Warehouses</span>
                    </a>
                </li>
                @endcan

                @can('manage-todos')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('todos.index') }}">
                        <i class="ri-checkbox-circle-line"></i> <span data-key="t-todo-list">Todo List</span>
                    </a>
                </li>
                @endcan

                @canany(['manage-users', 'manage-roles', 'view-activity-logs'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSettings">
                        <i class="ri-settings-4-line"></i> <span data-key="t-settings">Settings</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarSettings">
                        <ul class="nav nav-sm flex-column">
                            @can('manage-users')
                            <li class="nav-item">
                                <a href="{{ route('settings.index') }}" class="nav-link" data-key="t-general-settings">General Settings</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link" data-key="t-users">Users</a>
                            </li>
                            @endcan
                            @can('manage-roles')
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link" data-key="t-roles">Roles & Permissions</a>
                            </li>
                            @endcan
                            @can('view-activity-logs')
                            <li class="nav-item">
                                <a href="{{ route('activity-logs.index') }}" class="nav-link" data-key="t-activity-logs">System Activity Log</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
