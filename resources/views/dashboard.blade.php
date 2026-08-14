<x-app-layout>
    <x-slot name="header">
        <div class="row mb-3 pb-1">
            <div class="col-12">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-16 mb-1">Good Morning, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted mb-0">Here's what's happening with your store today.</p>
                    </div>
                </div><!-- end card header -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </x-slot>

    <div class="row">
        <!-- This Month Purchases -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Purchases (This Month)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($thisMonthPurchases, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="ri-shopping-cart-2-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Purchases -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Purchases</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($totalPurchases, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="ri-shopping-cart-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Sales (This Month)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($thisMonthSales, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="ri-money-dollar-circle-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Sales</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($totalSales, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="ri-hand-coin-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row-->

    <div class="row">
        <!-- Customers Due Balance -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-warning-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Customers Due Balance</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($customerBalance, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning rounded fs-3">
                                <i class="ri-user-unfollow-line text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Due Balance -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-danger-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Vendors Payables</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($vendorBalance, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger rounded fs-3">
                                <i class="ri-store-line text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stock Value -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-info-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Stock Value</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($totalStockValue, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info rounded fs-3">
                                <i class="ri-inbox-archive-line text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Self Balance -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-success-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">In Hand / Bank (Dr)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span>{{ number_format($selfBalance, 2) }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success rounded fs-3">
                                <i class="ri-wallet-3-line text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row-->

    <div class="row">
        <!-- Revenue Chart & Metrics -->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Revenue vs Expenses vs Profit</h4>
                </div>
                <div class="card-header p-0 border-0 bg-light-subtle">
                    <div class="row g-0 text-center">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $salesCountThisMonth }}">{{ $salesCountThisMonth }}</span></h5>
                                <p class="text-muted mb-0">Sales (This Month)</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1 text-danger"><span class="counter-value" data-target="{{ $expensesThisMonth }}">{{ number_format($expensesThisMonth, 2) }}</span></h5>
                                <p class="text-muted mb-0">Expenses</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1 text-success"><span class="counter-value" data-target="{{ $grossProfitThisMonth }}">{{ number_format($grossProfitThisMonth, 2) }}</span></h5>
                                <p class="text-muted mb-0">Gross Profit</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0 border-end-0">
                                <h5 class="mb-1 text-primary"><span class="counter-value" data-target="{{ $netProfitThisMonth }}">{{ number_format($netProfitThisMonth, 2) }}</span></h5>
                                <p class="text-muted mb-0">Net Profit</p>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                </div><!-- end card header -->
                <div class="card-body p-0 pb-2">
                    <div class="w-100" style="padding: 15px;">
                        <canvas id="revenueChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

    <div class="row">
        <!-- Top Selling Products -->
        <div class="col-xl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Top Selling Products</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Avg Price</th>
                                    <th scope="col">Sold Qty</th>
                                    <th scope="col">Sales Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bestSellingProducts as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">{{ $item->product->name ?? 'Unknown' }}</div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->avg_price, 2) }}</td>
                                    <td><span class="text-success">{{ $item->total_quantity }}</span></td>
                                    <td class="fw-medium">{{ number_format($item->total_sales_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Customers -->
        <div class="col-xl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Top Customers</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Total Purchase</th>
                                    <th scope="col">Customer Dues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bestCustomers as $customer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">{{ $customer->customer->name ?? 'Walk-in Customer' }}</div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($customer->total_spent, 2) }}</td>
                                    <td class="{{ $customer->total_dues > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format(max(0, $customer->total_dues), 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($revenueDates),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($revenueData),
                            borderColor: '#0ab39c',
                            backgroundColor: 'rgba(10, 179, 156, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Expenses',
                            data: @json($expenseData),
                            borderColor: '#f06548',
                            backgroundColor: 'rgba(240, 101, 72, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Net Profit',
                            data: @json($profitData),
                            borderColor: '#299cdb',
                            backgroundColor: 'rgba(41, 156, 219, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
