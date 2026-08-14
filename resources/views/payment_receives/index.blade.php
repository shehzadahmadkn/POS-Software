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
                            <h5 class="card-title mb-0">Payment Receiving</h5>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Create New
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="paymentsTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ref#</th>
                                    <th>From</th>
                                    <th>Received by</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Amount</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $key => $payment)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $payment->reference_no }}</td>
                                    <td>
                                        @if($payment->from)
                                            {{ $payment->from->name }} <span class="badge bg-light text-dark">{{ ucfirst($payment->from->type) }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $payment->account->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}</td>
                                    <td>{{ $payment->note }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item view-statement" 
                                                        data-type="{{ $payment->from ? $payment->from->type : '' }}" 
                                                        data-id="{{ $payment->from_account_id }}"
                                                        data-name="{{ $payment->from ? $payment->from->name : '' }}">
                                                        <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View Statement
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('payment_receives.destroy', $payment->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this payment?')">
                                                            <i class="ri-delete-bin-line align-bottom me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
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

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <form action="{{ route('payment_receives.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="createModalLabel">Receive Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">From (Customer/Vendor)</label>
                                <select name="from" id="fromEntity" class="form-select" required>
                                    <option value="">Select Customer or Vendor</option>
                                    <optgroup label="Customers">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}" data-balance="{{ number_format($c->balance, 2) }}">{{ $c->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendors">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}" data-balance="{{ number_format($v->balance, 2) }}">{{ $v->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <div class="mt-2 text-primary fw-bold fs-6" id="entityBalance" style="display: none;">
                                    Current Balance: <span id="balanceValue">0.00</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account (Received By)</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statement Modal -->
    <div class="modal fade" id="statementModal" tabindex="-1" aria-labelledby="statementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statementModalLabel">Account Statement: <span id="statementAccountName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" class="row g-3 mb-4">
                        <input type="hidden" id="statementAccountId" name="id">
                        <input type="hidden" id="statementAccountType" name="type">
                        <div class="col-auto">
                            <label class="visually-hidden">From Date</label>
                            <input type="date" class="form-control" id="fromDate" name="from_date">
                        </div>
                        <div class="col-auto">
                            <label class="visually-hidden">To Date</label>
                            <input type="date" class="form-control" id="toDate" name="to_date">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary mb-3">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="statementTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody id="statementBody">
                                <tr><td colspan="5" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="window.print()">Print Statement</button>
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
            $('#paymentsTable').DataTable({
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
                order: [[0, 'desc']]
            });

            // Show dynamic balance
            $('#fromEntity').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                if (selectedOption.val() !== "") {
                    var balance = selectedOption.data('balance');
                    $('#balanceValue').text(balance);
                    $('#entityBalance').fadeIn();
                } else {
                    $('#entityBalance').fadeOut();
                }
            });

            // Statement Modal Logic
            function loadStatement(type, id, from_date = null, to_date = null) {
                $('#statementBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                $.ajax({
                    url: '{{ route("statement.fetch") }}',
                    type: 'GET',
                    data: { type: type, id: id, from_date: from_date, to_date: to_date },
                    success: function(response) {
                        if (response.success) {
                            let rows = '';
                            if (response.data.length === 0) {
                                rows = '<tr><td colspan="5" class="text-center">No transactions found.</td></tr>';
                            } else {
                                response.data.forEach(function(tx) {
                                    let debitText = parseFloat(tx.debit) > 0 ? parseFloat(tx.debit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-';
                                    let creditText = parseFloat(tx.credit) > 0 ? parseFloat(tx.credit).toLocaleString(undefined, {minimumFractionDigits: 2}) : '-';
                                    let balanceText = parseFloat(tx.balance).toLocaleString(undefined, {minimumFractionDigits: 2});
                                    rows += `<tr>
                                        <td>${tx.date}</td>
                                        <td>${tx.description}</td>
                                        <td class="text-end">${debitText}</td>
                                        <td class="text-end">${creditText}</td>
                                        <td class="text-end fw-bold">${balanceText}</td>
                                    </tr>`;
                                });
                            }
                            $('#statementBody').html(rows);
                        } else {
                            $('#statementBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load statement.</td></tr>');
                        }
                    },
                    error: function() {
                        $('#statementBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading statement.</td></tr>');
                    }
                });
            }

            $('.view-statement').on('click', function() {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let name = $(this).data('name');
                
                $('#statementAccountId').val(id);
                $('#statementAccountType').val(type);
                $('#statementAccountName').text(name);
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                
                $('#fromDate').val(`${yyyy}-${mm}-01`);
                $('#toDate').val(`${yyyy}-${mm}-${dd}`);
                
                $('#statementModal').modal('show');
                loadStatement(type, id, $('#fromDate').val(), $('#toDate').val());
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#statementAccountId').val();
                let type = $('#statementAccountType').val();
                let from_date = $('#fromDate').val();
                let to_date = $('#toDate').val();
                loadStatement(type, id, from_date, to_date);
            });
        });
    </script>
    @endpush
</x-app-layout>
