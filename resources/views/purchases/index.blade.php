<x-app-layout>
    @push('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    @endpush

    <x-slot name="header">
        <div class="row mb-3 pb-1">
            <div class="col-12">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-16 mb-1">Purchases Management</h4>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <a href="{{ route('purchases.create') }}" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Record New Purchase</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">Purchase History</h5>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('purchases.index') }}">
                        <div class="row g-3 mb-4 align-items-end p-3 bg-light-subtle rounded border border-dashed border-primary-subtle">
                            <div class="col-md-5">
                                <label for="from_date" class="form-label text-muted text-uppercase fw-semibold mb-1 fs-12">From Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="ri-calendar-2-line"></i></span>
                                    <input type="date" class="form-control bg-white" id="from_date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="to_date" class="form-label text-muted text-uppercase fw-semibold mb-1 fs-12">To Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="ri-calendar-event-line"></i></span>
                                    <input type="date" class="form-control bg-white" id="to_date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-fill me-1 align-bottom"></i> Filter Results</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="purchasesTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Inv#</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables AJAX will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: #f4f5f8; border: none;">
                <div class="modal-body p-0" style="overflow: hidden; height: 100vh;">
                    <iframe id="editIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="activityIframe" src="" style="width: 100%; height: 400px; border: none;"></iframe>
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
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
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
            $(document).on('click', '.edit-purchase-btn, .view-purchase-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                $('#editIframe').attr('src', url);
                $('#editModal').modal('show');
            });

            $(document).on('click', '.view-activity-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                $('#activityIframe').attr('src', url);
                $('#activityModal').modal('show');
            });

            $('#purchasesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('purchases.index') }}',
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id', searchable: false },
                    { data: 'invoice_no', name: 'id' },
                    { data: 'vendor_name', name: 'vendor.name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'net_amount', name: 'net_amount' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
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
            
            // Prevent form submit, reload table instead
            $('form').on('submit', function(e) {
                if($(this).attr('method') === 'GET') {
                    e.preventDefault();
                    $('#purchasesTable').DataTable().ajax.reload();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
