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
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-bar-chart-box-line me-2"></i> Product Wise Sales Report</h4>
                            <p class="text-muted mb-0">Monitor total unit sales volumes, average prices, and overall product revenue performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product and Date Filters -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.product_wise_sales') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted mb-1">Select Product</label>
                                <select name="product_id" id="productSelect" class="form-select border-2" style="border: 2px solid #7f8c8d !important;">
                                    <option value="">All Products</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
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
                                <a href="{{ route('reports.product_wise_sales') }}" class="btn btn-light w-100 py-2"><i class="ri-refresh-line align-bottom me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ri-table-line me-2"></i> Sales Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="productSalesTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Total Quantity Sold</th>
                                    <th class="text-end">Average Price</th>
                                    <th class="text-end">Total Sales Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $key => $sale)
                                @php
                                    $avgPrice = $sale->total_qty > 0 ? $sale->total_amount / $sale->total_qty : 0;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $sale->product->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->product->category->name ?? 'Uncategorized' }}</td>
                                    <td class="text-center fw-bold">{{ number_format($sale->total_qty, 0) }}</td>
                                    <td class="text-end fw-semibold text-body">{{ number_format($avgPrice, 2) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold border-top border-2 d-none d-print-table-row">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end">Total:</td>
                                    <td class="text-center">{{ number_format($sales->sum('total_qty'), 0) }}</td>
                                    <td></td>
                                    <td class="text-end text-success">{{ number_format($sales->sum('total_amount'), 2) }}</td>
                                </tr>
                            </tfoot>
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
            $('#productSelect').select2({
                theme: 'bootstrap-5'
            });

            $('#productSalesTable').DataTable({
                dom: "<'row mb-3'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-soft-secondary btn-sm', footer: true },
                    { extend: 'csv', className: 'btn btn-soft-secondary btn-sm', footer: true },
                    { extend: 'excel', className: 'btn btn-soft-secondary btn-sm', footer: true },
                    { extend: 'print', className: 'btn btn-soft-secondary btn-sm', footer: true }
                ],
                responsive: true,
                order: [[3, 'desc']] // Order by Total Quantity Sold descending
            });
        });
    </script>
    @endpush
</x-app-layout>
