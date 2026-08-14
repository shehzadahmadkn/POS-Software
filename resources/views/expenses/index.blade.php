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
                            <h5 class="card-title mb-0">Expenses</h5>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{ route('expense_categories.index') }}" class="btn btn-secondary">Categories</a>
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
                        <table id="expensesTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ref#</th>
                                    <th>Category</th>
                                    <th>Account</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Amount</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $key => $expense)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $expense->reference_no }}</td>
                                    <td>{{ $expense->category ? $expense->category->name : 'N/A' }}</td>
                                    <td>{{ $expense->account ? $expense->account->name : 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}</td>
                                    <td>{{ $expense->note }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                    <td>
                                        @can('delete-expense')
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure you want to delete this expense?')">
                                                <i class="ri-delete-bin-line align-bottom me-1"></i> Delete
                                            </button>
                                        </form>
                                        @endcan
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
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="createModalLabel">Create Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Account</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="">Select Business Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="expense_category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01">
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
            $('#expensesTable').DataTable({
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
        });
    </script>
    @endpush
</x-app-layout>
