<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border: 2px solid #7f8c8d !important;
            min-height: 38px !important;
            display: flex !important;
            align-items: center !important;
            border-radius: 6px !important;
        }
    </style>
    @endpush

    <!-- Page Header -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-user-shared-line me-2"></i> Customer Wise Sales Report</h4>
                            <p class="text-muted mb-0">Track total orders, payments, outstanding dues for all customers, or view product-wise metrics for a specific customer.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.customer_wise_sales') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1">Select Customer</label>
                                <select name="customer_id" id="customerSelect" class="form-select border-2" style="border: 2px solid #7f8c8d !important;">
                                    <option value="">All Customers</option>
                                    @foreach($customersList as $c)
                                        <option value="{{ $c->id }}" {{ $selectedCustomerId == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} {{ $c->phone ? "({$c->phone})" : "" }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1">From Date</label>
                                <input type="date" name="from_date" class="form-control border-2" style="border: 2px solid #7f8c8d !important;" value="{{ $from }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1">To Date</label>
                                <input type="date" name="to_date" class="form-control border-2" style="border: 2px solid #7f8c8d !important;" value="{{ $to }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-filter-line align-bottom me-1"></i> Filter</button>
                                <a href="{{ route('reports.customer_wise_sales') }}" class="btn btn-light w-100 py-2"><i class="ri-refresh-line align-bottom me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ri-table-line me-2"></i> Sales Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="customerSalesTable" class="table table-bordered align-middle" style="width:100%">
                            @if ($selectedCustomerId)
                                <!-- Specific Customer Table -->
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Product</th>
                                        <th class="text-end" style="width: 180px;">Avg Price</th>
                                        <th class="text-center" style="width: 150px;">Quantity</th>
                                        <th class="text-end" style="width: 200px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotalQty = 0;
                                        $grandTotalAmount = 0;
                                    @endphp
                                    @foreach ($salesData as $key => $item)
                                    @php
                                        $avgPrice = $item->total_qty > 0 ? $item->total_amount / $item->total_qty : 0;
                                        $grandTotalQty += $item->total_qty;
                                        $grandTotalAmount += $item->total_amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-primary">{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end fw-semibold text-body">{{ number_format($avgPrice, 2) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->total_qty, 0) }}</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($item->total_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold d-none d-print-table-row">
                                    <tr>
                                        <td colspan="3" class="text-end">Total Summary:</td>
                                        <td class="text-center">{{ number_format($grandTotalQty, 0) }}</td>
                                        <td class="text-end text-success">{{ number_format($grandTotalAmount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            @else
                                <!-- All Customers Summary Table -->
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Customer Name</th>
                                        <th>Phone</th>
                                        <th class="text-center" style="width: 150px;">Total Orders</th>
                                        <th class="text-end" style="width: 180px;">Total Sales</th>
                                        <th class="text-end" style="width: 180px;">Total Paid</th>
                                        <th class="text-end" style="width: 200px;">Total Outstanding Dues</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $allOrdersCount = 0;
                                        $allTotalSales = 0;
                                        $allTotalPaid = 0;
                                        $allOutstanding = 0;
                                    @endphp
                                    @foreach ($customers as $key => $c)
                                    @php
                                        $ordersCount = $c->sales->count();
                                        $totalSales = $c->sales->sum('net_amount');
                                        $totalPaid = $c->sales->sum('paid_amount');
                                        $outstanding = $totalSales - $totalPaid;

                                        $allOrdersCount += $ordersCount;
                                        $allTotalSales += $totalSales;
                                        $allTotalPaid += $totalPaid;
                                        $allOutstanding += $outstanding;
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-semibold text-primary">{{ $c->name }}</td>
                                        <td>{{ $c->phone ?? '-' }}</td>
                                        <td class="text-center fw-semibold text-body">{{ number_format($ordersCount, 0) }}</td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($totalSales, 2) }}</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($totalPaid, 2) }}</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($outstanding, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold d-none d-print-table-row">
                                    <tr>
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td class="text-center">{{ number_format($allOrdersCount, 0) }}</td>
                                        <td class="text-end text-dark">{{ number_format($allTotalSales, 2) }}</td>
                                        <td class="text-end text-success">{{ number_format($allTotalPaid, 2) }}</td>
                                        <td class="text-end text-danger">{{ number_format($allOutstanding, 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .dt-buttons { display: flex; gap: 5px; margin-bottom: 10px; }
        .dataTables_filter { text-align: right; }
        .dataTables_filter label { display: flex; align-items: center; justify-content: flex-end; margin-bottom: 0; }
        .dataTables_filter input { margin-left: 10px; }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 search dropdown
            $('#customerSelect').select2({
                theme: 'bootstrap-5'
            });

            $('#customerSalesTable').DataTable({
                dom: "<'row mb-3'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'csv', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'excel', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'print', className: 'btn btn-soft-secondary btn-sm' }
                ],
                responsive: true,
                order: [[4, 'desc']] // Order by column index 4 (Total Sales or Amount) descending
            });
        });
    </script>
    @endpush
</x-app-layout>
