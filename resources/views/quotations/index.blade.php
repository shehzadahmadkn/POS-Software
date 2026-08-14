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
                            <h5 class="card-title mb-0">Quotation History</h5>
                        </div>
                        <div class="col-sm-auto">
                            <a href="{{ route('quotations.create') }}" class="btn btn-success">
                                <i class="ri-add-line align-bottom me-1"></i> Create Quotation
                            </a>
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
                        <table id="quotationsTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>Date</th>
                                    <th>Valid Till</th>
                                    <th width="150px" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotations as $key => $q)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $q->customer_name ?? $q->customer->name ?? 'Walk-in Customer' }}</td>
                                    <td>{{ $q->address ?? $q->customer->address ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($q->date)->format('Y-m-d') }}</td>
                                    <td>{{ $q->valid_till ? \Carbon\Carbon::parse($q->valid_till)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('quotations.show', $q->id) }}" class="btn btn-soft-primary btn-sm view-quotation-btn d-flex align-items-center gap-1">
                                                <i class="ri-eye-fill align-bottom fs-14"></i> View
                                            </a>
                                            @can('delete-quotation')
                                            <form action="{{ route('quotations.destroy', $q->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-soft-danger btn-sm d-flex align-items-center gap-1" onclick="return confirm('Are you sure you want to delete this quotation?')">
                                                    <i class="ri-delete-bin-line align-bottom fs-14"></i> Delete
                                                </button>
                                            </form>
                                            @endcan
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

    <!-- Fullscreen Edit/View Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: #f4f5f8; border: none;">
                <div class="modal-body p-0" style="overflow: hidden; height: 100vh;">
                    <iframe id="editIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
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
        function closeEditModal() {
            $('#editModal').modal('hide');
            $('#editIframe').attr('src', '');
        }

        function onEditSuccess() {
            closeEditModal();
            window.location.reload();
        }

        $(document).ready(function() {
            $(document).on('click', '.view-quotation-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                $('#editIframe').attr('src', url);
                $('#editModal').modal('show');
            });

            $('#quotationsTable').DataTable({
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
