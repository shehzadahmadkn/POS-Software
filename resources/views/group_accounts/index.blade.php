<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush



    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="groupAccountsList">
                <div class="card-header border-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Group Accounts</h5>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Create New
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="groupTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Group Name</th>
                                    <th>Customer Name</th>
                                    <th>Vendor Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupAccounts as $index => $group)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-medium">{{ $group->name }}</td>
                                        <td>{{ $group->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $group->vendor->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openStatementModal('group', {{ $group->id }}, '{{ addslashes($group->name) }}')">
                                                            <i class="ri-file-list-3-line align-bottom me-2 text-muted"></i> View Statement
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('group_accounts.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group account?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Delete
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

    <!-- Create Group Modal -->
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-success">
                    <h5 class="modal-title">Create Group Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('group_accounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Group Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Account</label>
                            <select name="customer_id" class="form-select select2-modal" required style="width: 100%;">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vendor Account</label>
                            <select name="vendor_id" class="form-select select2-modal" required style="width: 100%;">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Group</button>
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
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            display: flex;
            align-items: center;
        }
        .select2-container {
            z-index: 100000;
        }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            $('.select2-modal').select2({
                dropdownParent: $('#createGroupModal')
            });

            $('#groupTable').DataTable({
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
