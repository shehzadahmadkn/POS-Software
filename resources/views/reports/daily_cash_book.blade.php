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
                            <h4 class="mb-1 fw-bold text-primary"><i class="ri-book-read-line me-2"></i> Daily Cash Book</h4>
                            <p class="text-muted mb-0">Track daily ledger balances, cash inflow (Credits), and cash outflow (Debits).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.daily_cash_book') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-muted mb-1">Select Date</label>
                                <input type="date" name="date" class="form-control border-2" style="border: 2px solid #7f8c8d !important;" value="{{ $date }}">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-filter-line align-bottom me-1"></i> Filter</button>
                                <a href="{{ route('reports.daily_cash_book') }}" class="btn btn-light w-100 py-2"><i class="ri-refresh-line align-bottom me-1"></i> Today</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Summary Info -->
    <div class="row mt-3">
        <!-- Date -->
        <div class="col-md-2-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Date</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded-circle fs-3 text-info">
                                <i class="ri-calendar-todo-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opening Balance -->
        <div class="col-md-2-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Opening Balance</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="mb-0 fw-bold text-primary">{{ number_format($openingBalance, 2) }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded-circle fs-3 text-primary">
                                <i class="ri-play-circle-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Credits -->
        <div class="col-md-2-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Total Credits</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="mb-0 fw-bold text-success">{{ number_format($totalCredits, 2) }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-3 text-success">
                                <i class="ri-arrow-right-down-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Debits -->
        <div class="col-md-2-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Total Debits</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="mb-0 fw-bold text-danger">{{ number_format($totalDebits, 2) }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-3 text-danger">
                                <i class="ri-arrow-left-up-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Closing Balance -->
        <div class="col-md-2-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-semibold text-muted mb-0 fs-12">Closing Balance</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="mb-0 fw-bold {{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($closingBalance, 2) }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded-circle fs-3 text-warning">
                                <i class="ri-stop-circle-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Merged Cash Ledger Section -->
    <div class="row mt-2 mb-5">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ri-book-3-line me-2"></i> Cash Ledger Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cashBookTable" class="table table-bordered align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Ref #</th>
                                    <th>Account</th>
                                    <th>Notes</th>
                                    <th class="text-end" style="width: 200px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-success opacity-85">
                                    <td class="fw-bold text-success fs-14 py-2 border-end-0">
                                        <i class="ri-arrow-right-down-line me-2"></i> Credits / Inflow
                                    </td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0"></td>
                                </tr>
                                @forelse ($credits as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $item['ref'] }}</td>
                                    <td>{{ $item['account'] }}</td>
                                    <td>{{ $item['note'] }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="text-center text-muted small py-3 border-start-0 border-end-0">No inflow (Credits) found for this date.</td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0"></td>
                                </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td class="border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="text-end border-start-0">Total Credits:</td>
                                    <td class="text-end text-success fs-14">{{ number_format($totalCredits, 2) }}</td>
                                </tr>

                                <!-- Debits / Outflow Sub-header -->
                                <tr class="table-danger opacity-85">
                                    <td class="fw-bold text-danger fs-14 py-2 border-end-0">
                                        <i class="ri-arrow-left-up-line me-2"></i> Debits / Outflow
                                    </td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0"></td>
                                </tr>
                                @forelse ($debits as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $item['ref'] }}</td>
                                    <td>{{ $item['account'] }}</td>
                                    <td>{{ $item['note'] }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="text-center text-muted small py-3 border-start-0 border-end-0">No outflow (Debits) found for this date.</td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0"></td>
                                </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td class="border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="border-start-0 border-end-0"></td>
                                    <td class="text-end border-start-0">Total Debits:</td>
                                    <td class="text-end text-danger fs-14">({{ number_format($totalDebits, 2) }})</td>
                                </tr>
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
        /* 5-column grid helper for bootstrap */
        @media (min-width: 768px) {
            .col-md-2-4 {
                flex: 0 0 auto;
                width: 20%;
            }
        }
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
            $('#cashBookTable').DataTable({
                ordering: false, // Keep custom layout sections intact
                paging: false,   // Show all items to prevent section pagination splits
                dom: "<'row mb-3'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'csv', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'excel', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'print', className: 'btn btn-soft-secondary btn-sm' }
                ],
                responsive: true
            });
        });
    </script>
    @endpush
</x-app-layout>
