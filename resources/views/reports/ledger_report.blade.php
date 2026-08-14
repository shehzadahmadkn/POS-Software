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
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-refund-2-line me-2"></i> Ledger Statement Report</h4>
                            <p class="text-muted mb-0">Browse through individual business, customer, or vendor ledgers, or select all for a combined ledger view.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Fields -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.ledger_report') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted mb-1">Select Account / Entity</label>
                                <select name="account" id="accountSelect" class="form-select border-2" style="border: 2px solid #7f8c8d !important;">
                                    <option value="">All Accounts</option>
                                    <optgroup label="Business Accounts">
                                        @foreach($accounts as $acc)
                                            <option value="business_{{ $acc->id }}" {{ $accountKey == "business_{$acc->id}" ? 'selected' : '' }}>
                                                {{ $acc->name }} ({{ ucfirst($acc->type) }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Customer Accounts">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}" {{ $accountKey == "customer_{$c->id}" ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendor Accounts">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}" {{ $accountKey == "vendor_{$v->id}" ? 'selected' : '' }}>
                                                {{ $v->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
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
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-filter-line align-bottom me-1"></i> Filter</button>
                                <a href="{{ route('reports.ledger_report') }}" class="btn btn-light w-100 py-2"><i class="ri-refresh-line align-bottom me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Details Row -->
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="ri-account-box-line me-2"></i> {{ $selectedName }}</h5>
                    <div class="fs-14">
                        <span class="text-muted fw-semibold">Opening Balance:</span> <span class="fw-bold text-dark me-3">{{ number_format($openingBalance, 2) }}</span>
                        <span class="text-muted fw-semibold">Closing Balance:</span> <span class="fw-bold text-primary">{{ number_format($closingBalance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ledgerTable" class="table table-bordered align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Statement Description</th>
                                    <th class="text-end" style="width: 150px;">Debit</th>
                                    <th class="text-end" style="width: 150px;">Credit</th>
                                    <th class="text-end" style="width: 180px;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
            $('#accountSelect').select2({
                theme: 'bootstrap-5'
            });

            if ($('#ledgerTable').length) {
                $('#ledgerTable').DataTable({
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
                    ordering: false,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('reports.ledger_report') }}",
                        data: function (d) {
                            d.account = "{{ $accountKey }}";
                            d.from_date = "{{ $from }}";
                            d.to_date = "{{ $to }}";
                        },
                        dataSrc: function (json) {
                            window.currentOpeningBalance = json.openingBalance;
                            return json.data;
                        }
                    },
                    columns: [
                        { 
                            data: null, 
                            render: function(data, type, row, meta) {
                                return meta.row + 1 + meta.settings._iDisplayStart;
                            } 
                        },
                        { 
                            data: 'date',
                            render: function(data) {
                                return data ? data.split(' ')[0] : '-'; // format date simply
                            }
                        },
                        { data: 'account', className: 'fw-semibold text-primary' },
                        { data: 'description' },
                        { 
                            data: 'debit',
                            className: 'text-end text-success fw-semibold',
                            render: function(data) {
                                return parseFloat(data) > 0 ? parseFloat(data).toFixed(2) : '-';
                            }
                        },
                        { 
                            data: 'credit',
                            className: 'text-end text-danger fw-semibold',
                            render: function(data) {
                                return parseFloat(data) > 0 ? '-' + parseFloat(data).toFixed(2) : '-';
                            }
                        },
                        { 
                            data: 'balance',
                            className: 'text-end fw-bold text-dark',
                            render: function(data) {
                                return parseFloat(data).toFixed(2);
                            }
                        }
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();
                        var page = api.page.info().page;
                        
                        if (page === 0) {
                            var openingBalance = window.currentOpeningBalance;
                            if (openingBalance !== undefined) {
                                var html = `<tr>
                                    <td>0</td>
                                    <td>{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '-' }}</td>
                                    <td class="text-muted fw-semibold">{{ $selectedName }}</td>
                                    <td><strong>Opening Balance</strong></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end fw-bold text-dark">${parseFloat(openingBalance).toFixed(2)}</td>
                                </tr>`;
                                $(api.table().body()).prepend(html);
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
