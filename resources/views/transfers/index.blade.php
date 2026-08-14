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
                            <h5 class="card-title mb-0">Transfers</h5>
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
                        <table id="transfersTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ref#</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Amount</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $key => $transfer)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $transfer->reference_no }}</td>
                                    <td>
                                        @if($transfer->fromEntity)
                                            {{ $transfer->fromEntity->name }} <span class="badge bg-light text-dark">{{ ucfirst($transfer->fromEntity->type) }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($transfer->toEntity)
                                            {{ $transfer->toEntity->name }} <span class="badge bg-light text-dark">{{ ucfirst($transfer->toEntity->type) }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($transfer->date)->format('Y-m-d') }}</td>
                                    <td>{{ $transfer->note }}</td>
                                    <td>{{ number_format($transfer->amount, 2) }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="javascript:void(0);" class="dropdown-item edit-btn" 
                                                        data-id="{{ $transfer->id }}"
                                                        data-from="{{ $transfer->fromEntity ? $transfer->fromEntity->type : '' }}_{{ $transfer->from_account_id }}"
                                                        data-to="{{ $transfer->toEntity ? $transfer->toEntity->type : '' }}_{{ $transfer->to_account_id }}"
                                                        data-amount="{{ $transfer->amount }}"
                                                        data-date="{{ $transfer->date }}"
                                                        data-note="{{ $transfer->note }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this transfer?')">
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
                <form action="{{ route('transfers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="createModalLabel">Create Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">From</label>
                                <select name="from" id="createFrom" class="form-select" required>
                                    <option value="">Select From</option>
                                    <optgroup label="Business Accounts">
                                        @foreach($accounts as $acc)
                                            <option value="business_{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Customers">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendors">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To</label>
                                <select name="to" id="createTo" class="form-select" required>
                                    <option value="">Select To</option>
                                    <optgroup label="Business Accounts">
                                        @foreach($accounts as $acc)
                                            <option value="business_{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Customers">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendors">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </optgroup>
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
                        <button type="submit" class="btn btn-success">Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="editModalLabel">Edit Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">From</label>
                                <select name="from" id="editFrom" class="form-select" required>
                                    <option value="">Select From</option>
                                    <optgroup label="Business Accounts">
                                        @foreach($accounts as $acc)
                                            <option value="business_{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Customers">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendors">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To</label>
                                <select name="to" id="editTo" class="form-select" required>
                                    <option value="">Select To</option>
                                    <optgroup label="Business Accounts">
                                        @foreach($accounts as $acc)
                                            <option value="business_{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Customers">
                                        @foreach($customers as $c)
                                            <option value="customer_{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Vendors">
                                        @foreach($vendors as $v)
                                            <option value="vendor_{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" id="editAmount" class="form-control" required min="0.01" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" id="editDate" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="editNote" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
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
            $('#transfersTable').DataTable({
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

            // Prevent selecting same From and To
            function validateAccounts(fromId, toId) {
                $(toId + ' option').prop('disabled', false);
                let selected = $(fromId).val();
                if(selected) {
                    $(toId + ' option[value="'+selected+'"]').prop('disabled', true);
                }
            }

            $('#createFrom').on('change', function() { validateAccounts('#createFrom', '#createTo'); });
            $('#editFrom').on('change', function() { validateAccounts('#editFrom', '#editTo'); });

            // Edit Modal logic
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $('#editForm').attr('action', '/transfers/' + id);
                
                $('#editFrom').val($(this).data('from'));
                $('#editTo').val($(this).data('to'));
                $('#editAmount').val($(this).data('amount'));
                $('#editDate').val($(this).data('date'));
                $('#editNote').val($(this).data('note'));
                
                $('#editModal').modal('show');
            });
        });
    </script>
    @endpush
</x-app-layout>
