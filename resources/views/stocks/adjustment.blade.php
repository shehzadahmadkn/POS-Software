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
                            <h5 class="card-title mb-0">Stock Adjustment History</h5>
                        </div>
                        <div class="col-sm-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="ri-add-line align-bottom me-1"></i> Create New
                            </button>
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
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="adjustmentsTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Ref</th>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Date</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-end">Quantity</th>
                                    <th>Note</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adjustments as $key => $adj)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td class="fw-semibold text-muted">{{ str_pad($adj->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <span class="fw-semibold text-primary">{{ $adj->product->name ?? 'N/A' }}</span>
                                        <div class="text-muted small">SKU: {{ $adj->product->sku ?? '-' }}</div>
                                    </td>
                                    <td>{{ $adj->location->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($adj->date)->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        @if($adj->type === 'addition')
                                            <span class="badge bg-success-subtle text-success fw-bold fs-12 px-2 py-1"><i class="ri-add-line"></i> + (Increase)</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger fw-bold fs-12 px-2 py-1"><i class="ri-subtract-line"></i> - (Decrease)</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        @if($adj->type === 'addition')
                                            <span class="text-success">+{{ $adj->quantity }}</span>
                                        @else
                                            <span class="text-danger">-{{ $adj->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $adj->reason ?? '-' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('stocks.adjustment.destroy', $adj->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this stock adjustment? This will revert the quantity changes.');" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm d-inline-flex align-items-center gap-1">
                                                <i class="ri-delete-bin-line"></i> Delete
                                            </button>
                                        </form>
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
                <form action="{{ route('stocks.adjustment.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="createModalLabel">Create Stock Adjustment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->name }} (SKU: {{ $prod->sku ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="addition">Addition (Increase Qty)</option>
                                    <option value="subtraction">Subtraction (Decrease Qty)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" required min="1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Note / Reference</label>
                                <input type="text" name="reason" class="form-control" placeholder="e.g. Damage, Inventory count correction">
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
            $('#adjustmentsTable').DataTable({
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
