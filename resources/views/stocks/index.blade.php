<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    @endpush

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                @if($filter === 'zero')
                                    Stock with Zero Quantity
                                @elseif($filter === 'above_zero')
                                    Stock with Above Zero Quantity
                                @elseif($filter === 'below_zero')
                                    Stock with Below Zero Quantity
                                @else
                                    Current Stock List
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <form action="{{ route('stocks.index') }}" method="GET" id="stockFilterForm">
                                @if($filter) <input type="hidden" name="filter" value="{{ $filter }}"> @endif
                                <label class="form-label fw-semibold text-muted mb-1">Warehouse</label>
                                <select class="form-select border-2" name="warehouse_id" onchange="document.getElementById('stockFilterForm').submit()" style="border: 2px solid #7f8c8d !important;">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ isset($warehouseId) && $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="stocksTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $key => $stock)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        <span class="fw-semibold text-primary">{{ $stock->product->name ?? 'N/A' }}</span>
                                        <div class="text-muted small">SKU: {{ $stock->product->sku ?? '-' }}</div>
                                    </td>
                                    <td>{{ $stock->product->category->name ?? 'Uncategorized' }}</td>
                                    <td class="fw-semibold">
                                        @if($stock->quantity < 0)
                                            <span class="text-danger">{{ $stock->quantity }}</span>
                                        @elseif($stock->quantity == 0)
                                            <span class="text-warning">0</span>
                                        @else
                                            <span class="text-success">{{ $stock->quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-soft-info btn-sm btn-detail d-inline-flex align-items-center gap-1" data-id="{{ $stock->product_id }}" data-name="{{ $stock->product->name ?? 'N/A' }}">
                                            <i class="ri-history-line"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Details Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <!-- Header -->
                <div class="modal-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="detailModalTitle">
                        <i class="ri-survey-line me-2"></i> Stock Ledger details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(1) brightness(2);"></button>
                </div>
                <!-- Body -->
                <div class="modal-body p-4">
                    <form id="filter-details-form" class="row g-3 align-items-end mb-4 p-3 rounded border">
                        <input type="hidden" id="modal-product-id" name="product_id">
                        
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-1">From Date</label>
                            <input type="date" class="form-control border-2" id="detail-from-date" style="border: 2px solid #7f8c8d !important;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-1">To Date</label>
                            <input type="date" class="form-control border-2" id="detail-to-date" style="border: 2px solid #7f8c8d !important;">
                        </div>
                        <input type="hidden" id="detail-warehouse-id" name="warehouse_id" value="{{ isset($warehouseId) ? $warehouseId : '' }}">
                        <div class="col-md-8 d-flex gap-2">
                            <button type="button" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-1" id="btn-modal-view">
                                <i class="ri-search-line"></i> View
                            </button>
                            <button type="button" class="btn btn-success w-100 py-2 d-flex align-items-center justify-content-center gap-1" id="btn-modal-print">
                                <i class="ri-printer-line"></i> Print
                            </button>
                            <button type="button" class="btn btn-secondary w-100 py-2" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>

                    <!-- Details Table -->
                    <div class="card border shadow-sm rounded">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-striped table-hover align-middle mb-0" id="modal-details-table">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Ref/Description</th>
                                            <th>Warehouse</th>
                                            <th class="text-end">Qty Change</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modal-details-tbody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Click View to fetch details...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
        .table-responsive { overflow-y: auto; }
        .sticky-top { position: sticky; top: 0; z-index: 10; background-color: #f8f9fa; }
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
            // Initialize main stock datatable
            $('#stocksTable').DataTable({
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
                order: [[3, 'desc']]
            });

            // Handle Detail Button Click
            $(document).on('click', '.btn-detail', function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');

                // Set modal parameters
                $('#modal-product-id').val(productId);
                $('#detailModalTitle').html(`<i class="ri-history-line me-2"></i> Stock Ledger details: ${productName}`);

                // Default date filter (30 days ago to today)
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const todayStr = `${yyyy}-${mm}-${dd}`;

                const firstDayStr = `${yyyy}-${mm}-01`;

                $('#detail-from-date').val(firstDayStr);
                $('#detail-to-date').val(todayStr);

                // Show modal
                $('#detailModal').modal('show');

                // Auto fetch details right away
                fetchStockDetails();
            });

            // Handle Modal View Button Click
            $('#btn-modal-view').on('click', function(e) {
                e.preventDefault();
                fetchStockDetails();
            });

            // Handle Modal Print Button Click
            $('#btn-modal-print').on('click', function(e) {
                e.preventDefault();
                const title = $('#detailModalTitle').text().trim();
                const fromDate = $('#detail-from-date').val();
                const toDate = $('#detail-to-date').val();
                const warehouse = '{{ (isset($warehouseId) && $warehouseId) ? $warehouses->where("id", $warehouseId)->first()->name : "All Warehouses" }}';
                
                // Get table body HTML
                const tbodyHtml = $('#modal-details-tbody').html();
                
                // Construct a printable window
                const printWindow = window.open('', '_blank', 'height=600,width=800');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>${title}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            body { font-family: 'Inter', system-ui, sans-serif; padding: 30px; color: #2c3e50; }
                            .print-header { border-bottom: 2px solid #3498db; padding-bottom: 15px; margin-bottom: 25px; }
                            .print-title { font-size: 24px; font-weight: 700; color: #2c3e50; margin: 0; }
                            .print-meta { font-size: 14px; color: #7f8c8d; margin-top: 5px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                            th { background-color: #f8f9fa !important; border-bottom: 2px solid #dee2e6 !important; font-weight: 600; }
                            td, th { padding: 10px 12px; font-size: 14px; border-bottom: 1px solid #dee2e6; }
                            .text-success { color: #198754 !important; font-weight: bold; }
                            .text-danger { color: #dc3545 !important; font-weight: bold; }
                            .badge { padding: 4px 8px; font-size: 12px; border-radius: 4px; font-weight: 600; display: inline-block; }
                            .bg-danger-subtle { background-color: #fce8e6; color: #ea4335; }
                            .bg-success-subtle { background-color: #e6f4ea; color: #137333; }
                            .bg-info-subtle { background-color: #e8f0fe; color: #1a73e8; }
                            @media print {
                                body { padding: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-header">
                            <h2 class="print-title">${title}</h2>
                            <div class="print-meta">
                                <strong>Period:</strong> ${fromDate} to ${toDate} &nbsp;|&nbsp; 
                                <strong>Warehouse:</strong> ${warehouse}
                            </div>
                        </div>
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ref/Description</th>
                                    <th>Warehouse</th>
                                    <th class="text-end">Qty Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tbodyHtml}
                            </tbody>
                        </table>
                        <script>
                            window.onload = function() {
                                setTimeout(function() {
                                    window.print();
                                    window.close();
                                }, 500);
                            };
                        <\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            });

            // Helper to fetch details via AJAX
            function fetchStockDetails() {
                const productId = $('#modal-product-id').val();
                const fromDate = $('#detail-from-date').val();
                const toDate = $('#detail-to-date').val();
                const warehouseId = $('#detail-warehouse-id').val();

                const tbody = $('#modal-details-tbody');
                tbody.html('<tr><td colspan="5" class="text-center py-4"><i class="ri-loader-4-line ri-spin fs-24 align-middle text-primary me-2"></i> Loading stock details...</td></tr>');

                $.ajax({
                    url: "{{ route('stocks.details') }}",
                    type: "GET",
                    data: {
                        product_id: productId,
                        from_date: fromDate,
                        to_date: toDate,
                        warehouse_id: warehouseId
                    },
                    dataType: "json",
                    success: function(res) {
                        tbody.empty();
                        if (res.success && res.logs.length > 0) {
                            res.logs.forEach(log => {
                                const qtyClass = log.qty > 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
                                const qtyPrefix = log.qty > 0 ? '+' : '';
                                tbody.append(`
                                    <tr>
                                        <td>${log.date}</td>
                                        <td><span class="badge ${log.type === 'Sale' ? 'bg-danger-subtle text-danger' : log.type === 'Purchase' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info'}">${log.type}</span></td>
                                        <td>${log.ref}</td>
                                        <td>${log.warehouse}</td>
                                        <td class="text-end ${qtyClass}">${qtyPrefix}${log.qty}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No stock transactions found for the selected range.</td></tr>');
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        tbody.html('<tr><td colspan="5" class="text-center text-danger py-4"><i class="ri-error-warning-line me-1"></i> Failed to retrieve stock history logs.</td></tr>');
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
