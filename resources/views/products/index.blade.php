<x-app-layout>
    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .dt-buttons { display: flex; gap: 5px; margin-bottom: 10px; }
        .dataTables_filter { text-align: right; }
        .dataTables_filter label { display: flex; align-items: center; justify-content: flex-end; margin-bottom: 0; }
        .dataTables_filter input { margin-left: 10px; width: 300px; }
    </style>
    @endpush

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom align-items-center d-flex justify-content-between">
                    <h4 class="card-title mb-0">Product</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProductModal">
                            <i class="ri-add-line align-bottom me-1"></i> Create new
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="productsTable" class="table table-bordered dt-responsive nowrap align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Purchase Price</th>
                                    <th>Sales Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                    <td>{{ number_format($product->cost_price, 2) }}</td>
                                    <td>{{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('products.destroy',$product->id) }}" method="POST" class="d-inline">
                                            @can('edit-product')
                                            <button type="button" class="btn btn-sm btn-info" onclick="editProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->sku }}', '{{ $product->category_id }}', '{{ $product->cost_price }}', '{{ $product->selling_price }}')">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            @endcan
                                            @csrf
                                            @method('DELETE')
                                            @can('delete-product')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')"><i class="ri-delete-bin-line"></i></button>
                                            @endcan
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

    <!-- Create Product Modal -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProductModalLabel">Create New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Code (SKU)</label>
                                <div class="input-group">
                                    <input type="text" name="sku" id="create_sku" class="form-control" placeholder="Leave blank to auto-generate">
                                    <button class="btn btn-outline-primary" type="button" onclick="generateSKU('create_sku')">Generate</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="Product Name">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Purchase Price</label>
                                <input type="number" name="cost_price" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sales Price</label>
                                <input type="number" name="selling_price" class="form-control" required min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end w-100 gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editProductForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Code (SKU)</label>
                                <div class="input-group">
                                    <input type="text" name="sku" id="edit_sku" class="form-control">
                                    <button class="btn btn-outline-primary" type="button" onclick="generateSKU('edit_sku')">Generate</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Category</label>
                                <select name="category_id" id="edit_category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Purchase Price</label>
                                <input type="number" name="cost_price" id="edit_cost_price" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sales Price</label>
                                <input type="number" name="selling_price" id="edit_selling_price" class="form-control" required min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end w-100 gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-info text-white">Save Changes</button>
                        </div>
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
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
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
                order: [[0, 'asc']],
                language: {
                    search: "",
                    searchPlaceholder: "Live search..."
                }
            });
        });

        function generateSKU(inputId) {
            const randomSKU = 'PRD-' + Math.random().toString(36).substring(2, 10).toUpperCase();
            document.getElementById(inputId).value = randomSKU;
        }

        function editProduct(id, name, sku, category_id, cost_price, selling_price) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_sku').value = sku;
            document.getElementById('edit_category_id').value = category_id;
            document.getElementById('edit_cost_price').value = cost_price;
            document.getElementById('edit_selling_price').value = selling_price;
            document.getElementById('editProductForm').action = '/products/' + id;
            var editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            editModal.show();
        }
    </script>
    @endpush
</x-app-layout>
