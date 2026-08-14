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
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-store-2-line me-2"></i> Stock Valuation Report</h4>
                            <p class="text-muted mb-0">Track total items in stock, unit prices, and overall cost and retail valuations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Report Table Card -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockReportTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product Name</th>
                                    <th class="text-center">Current Stock</th>
                                    <th class="text-end" style="width: 150px;">Cost Price</th>
                                    <th class="text-end" style="width: 150px;">Selling Price</th>
                                    <th class="text-end" style="width: 180px;">Total Cost Value</th>
                                    <th class="text-end" style="width: 180px;">Total Retail Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotalCostValue = 0;
                                    $grandTotalRetailValue = 0;
                                @endphp
                                @foreach ($products as $key => $product)
                                @php
                                    $currentStock = $product->stocks->sum('quantity');
                                    $totalCostValue = $currentStock * $product->cost_price;
                                    $totalRetailValue = $currentStock * $product->selling_price;
                                    $grandTotalCostValue += $totalCostValue;
                                    $grandTotalRetailValue += $totalRetailValue;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $product->name }}</td>
                                    <td class="text-center fw-bold">{{ number_format($currentStock, 0) }}</td>
                                    <td class="text-end fw-semibold text-body">{{ number_format($product->cost_price, 2) }}</td>
                                    <td class="text-end fw-semibold text-body">{{ number_format($product->selling_price, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($totalCostValue, 2) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($totalRetailValue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Total Valuation:</td>
                                    <td class="text-end text-dark">{{ number_format($grandTotalCostValue, 2) }}</td>
                                    <td class="text-end text-success">{{ number_format($grandTotalRetailValue, 2) }}</td>
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
            $('#stockReportTable').DataTable({
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
                order: [[2, 'desc']] // Order by Current Stock descending (column index 2)
            });
        });
    </script>
    @endpush
</x-app-layout>
