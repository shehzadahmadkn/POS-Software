<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    @endpush



    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="customerList">
                <div class="card-header border-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Customer Accounts</h5>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{ route('accounts.create') }}" class="btn btn-success add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Create New
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="customerTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-medium">{{ $customer->name }}</td>
                                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                                        <td>{{ $customer->address ?? 'N/A' }}</td>
                                        <td class="fw-bold {{ $customer->balance < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $customer->balance < 0 ? '-' : '' }}{{ number_format(abs($customer->balance), 2) }}
                                        </td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openStatementModal('customer', {{ $customer->id }}, '{{ addslashes($customer->name) }}')">
                                                            <i class="ri-file-list-3-line align-bottom me-2 text-muted"></i> View Statement
                                                        </a>
                                                    </li>
                                                    @can('edit-customer')
                                                    <li>
                                                        <a class="dropdown-item edit-item-btn" href="javascript:void(0);" onclick="openEditModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ addslashes($customer->phone) }}', '{{ addslashes($customer->address) }}')">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                        </a>
                                                    </li>
                                                    @endcan
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

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title">Edit Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact #</label>
                            <input type="text" name="phone" id="editPhone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" id="editAddress" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Date Range Modal -->
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-primary">
                    <h5 class="modal-title">Select Time Frame</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="statementType">
                    <input type="hidden" id="statementId">
                    <input type="hidden" id="statementName">
                    <div class="mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" id="fromDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" id="toDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-100" onclick="fetchStatement()">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Width Statement Modal -->
    <div class="modal fade" id="statementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="statementModalTitle">Account Statement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody id="statementTableBody">
                                <!-- Populated via AJAX -->
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
    <!--datatable js-->
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
        $(document).ready(function () {
            $('#customerTable').DataTable({
                dom: "<'row mb-3'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    { extend: 'copy', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'csv', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'excel', className: 'btn btn-soft-secondary btn-sm' },
                    { extend: 'print', className: 'btn btn-soft-secondary btn-sm' }
                ],
                responsive: true
            });
        });

        function openEditModal(id, name, phone, address) {
            document.getElementById('editForm').action = `/customers/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editPhone').value = phone;
            document.getElementById('editAddress').value = address;
            new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
        }

        function openStatementModal(type, id, name) {
            document.getElementById('statementType').value = type;
            document.getElementById('statementId').value = id;
            document.getElementById('statementName').value = name;
            new bootstrap.Modal(document.getElementById('dateRangeModal')).show();
        }

        function fetchStatement() {
            const type = document.getElementById('statementType').value;
            const id = document.getElementById('statementId').value;
            const name = document.getElementById('statementName').value;
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;

            // Hide date modal
            bootstrap.Modal.getInstance(document.getElementById('dateRangeModal')).hide();
            
            // Show statement modal
            document.getElementById('statementModalTitle').innerText = `Statement for ${name} (${from || 'All Time'} to ${to})`;
            document.getElementById('statementTableBody').innerHTML = `<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>`;
            const statementModal = new bootstrap.Modal(document.getElementById('statementModal'));
            statementModal.show();

            // Fetch AJAX
            fetch(`/statement?type=${type}&id=${id}&from=${from}&to=${to}`)
                .then(res => res.json())
                .then(response => {
                    let html = '';
                    if (response.success && response.data) {
                        let data = response.data;
                        data.forEach(row => {
                            html += `<tr>
                                <td>${row.date}</td>
                                <td>${row.description}</td>
                                <td class="text-success">${row.debit > 0 ? parseFloat(row.debit).toFixed(2) : '-'}</td>
                                <td class="text-danger">${row.credit > 0 ? parseFloat(row.credit).toFixed(2) : '-'}</td>
                                <td class="fw-bold">${parseFloat(row.balance).toFixed(2)}</td>
                            </tr>`;
                        });
                        if(data.length === 0) {
                            html = `<tr><td colspan="5" class="text-center">No transactions found for the selected time frame.</td></tr>`;
                        }
                    } else {
                        html = `<tr><td colspan="5" class="text-center text-danger">Failed to load statement data.</td></tr>`;
                    }
                    document.getElementById('statementTableBody').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('statementTableBody').innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load statement data.</td></tr>`;
                });
        }
    </script>
    @endpush
</x-app-layout>
