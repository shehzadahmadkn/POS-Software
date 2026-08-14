<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    @endpush

    <!-- Page Header -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-line-chart-line me-2"></i> Profit & Loss Report</h4>
                            <p class="text-muted mb-0">Track product-wise gross margins, expenses, discounts, and total net profits.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filters -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('reports.profit_loss') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted mb-1">From Date</label>
                                <input type="date" name="from_date" class="form-control border-2" style="border: 2px solid #7f8c8d !important;" value="{{ $from }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted mb-1">To Date</label>
                                <input type="date" name="to_date" class="form-control border-2" style="border: 2px solid #7f8c8d !important;" value="{{ $to }}">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-filter-line align-bottom me-1"></i> Filter</button>
                                <a href="{{ route('reports.profit_loss') }}" class="btn btn-light w-100 py-2"><i class="ri-refresh-line align-bottom me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Product-wise Profit / Loss Table -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ri-table-line me-2"></i> Product-wise Gross Profits</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="report-table-wrapper">
                        <table id="profitLossTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th class="text-end">Avg Purchase Price</th>
                                    <th class="text-end">Avg Sale Price</th>
                                    <th class="text-center">Sold Qty</th>
                                    <th class="text-end">Profit / Unit</th>
                                    <th class="text-end">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold text-primary">{{ $item['product'] }}</span>
                                        <div class="text-muted small">SKU: {{ $item['sku'] }}</div>
                                    </td>
                                    <td class="text-end fw-semibold text-body">{{ number_format($item['avg_purchase_price'], 2) }}</td>
                                    <td class="text-end fw-semibold text-body">{{ number_format($item['avg_sale_price'], 2) }}</td>
                                    <td class="text-center fw-bold">{{ $item['sold_qty'] }}</td>
                                    <td class="text-end fw-semibold {{ $item['profit_per_unit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $item['profit_per_unit'] >= 0 ? '+' : '' }}{{ number_format($item['profit_per_unit'], 2) }}
                                    </td>
                                    <td class="text-end fw-bold {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $item['profit'] >= 0 ? '+' : '' }}{{ number_format($item['profit'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td class="text-end">Total Profit (Gross):</td>
                                    <td class="text-end text-primary">{{ number_format($totalProductProfit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td class="text-end">Expenses:</td>
                                    <td class="text-end text-danger">- {{ number_format($totalExpenses, 2) }}</td>
                                </tr>
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td class="text-end">Discounts Given:</td>
                                    <td class="text-end text-warning">- {{ number_format($totalDiscounts, 2) }}</td>
                                </tr>
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td class="text-end">Net Profit:</td>
                                    <td class="text-end text-success">{{ number_format($netProfit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row mt-3">
        <!-- Total Gross Product Profit -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Total Profit (Gross)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalProductProfit, 2) }}</h2>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded-circle fs-3 text-primary">
                                <i class="ri-price-tag-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Expenses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($totalExpenses, 2) }}</h2>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-3 text-danger">
                                <i class="ri-wallet-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Discounts -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Discounts Given</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalDiscounts, 2) }}</h2>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded-circle fs-3 text-warning">
                                <i class="ri-percent-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Net Profit</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($netProfit, 2) }}</h2>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-3 text-success">
                                <i class="ri-line-chart-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
    <style>
        @media screen {
            #report-table-wrapper tfoot { display: none !important; }
        }
        .dt-buttons { display: flex; gap: 5px; margin-bottom: 10px; }
        .dataTables_filter { text-align: right; }
        .dataTables_filter label { display: flex; align-items: center; justify-content: flex-end; margin-bottom: 0; }
        .dataTables_filter input { margin-left: 10px; }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
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
            var appendTotalsToBody = function(data) {
                // Manually inject the totals as standard rows into the export body for CSV/Excel/Copy
                data.body.push(['', '', '', '', '', 'Total Profit (Gross):', '{{ number_format($totalProductProfit, 2) }}']);
                data.body.push(['', '', '', '', '', 'Expenses:', '- {{ number_format($totalExpenses, 2) }}']);
                data.body.push(['', '', '', '', '', 'Discounts Given:', '- {{ number_format($totalDiscounts, 2) }}']);
                data.body.push(['', '', '', '', '', 'Net Profit:', '{{ number_format($netProfit, 2) }}']);
            };

            var customizePrint = function(win) {
                // Manually inject the formatted tfoot HTML into the print window
                var tfootHtml = '<tfoot style="font-weight: bold; border-top: 2px solid #ddd;">' +
                    '<tr><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">Total Profit (Gross):</td><td style="text-align: right; color: #0d6efd;">{{ number_format($totalProductProfit, 2) }}</td></tr>' +
                    '<tr><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">Expenses:</td><td style="text-align: right; color: #dc3545;">- {{ number_format($totalExpenses, 2) }}</td></tr>' +
                    '<tr><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">Discounts Given:</td><td style="text-align: right; color: #ffc107;">- {{ number_format($totalDiscounts, 2) }}</td></tr>' +
                    '<tr><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">Net Profit:</td><td style="text-align: right; color: #198754;">{{ number_format($netProfit, 2) }}</td></tr>' +
                    '</tfoot>';
                $(win.document.body).find('table').append(tfootHtml);
            };

            $('#profitLossTable').DataTable({
                dom: "<'row mb-3'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-soft-secondary btn-sm', footer: false, customizeData: appendTotalsToBody },
                    { extend: 'csv', className: 'btn btn-soft-secondary btn-sm', footer: false, customizeData: appendTotalsToBody },
                    { extend: 'excel', className: 'btn btn-soft-secondary btn-sm', footer: false, customizeData: appendTotalsToBody },
                    { extend: 'print', className: 'btn btn-soft-secondary btn-sm', footer: false, customize: customizePrint }
                ],
                responsive: true,
                order: [[6, 'desc']] // Order by profit column descending
            });
        });
    </script>
    @endpush
</x-app-layout>
