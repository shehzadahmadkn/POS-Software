<x-app-layout>
    @push('styles')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .card-custom {
            background: var(--vz-card-bg, #fff); border: 1px solid var(--vz-border-color, #e2e5e8); border-radius: 8px;
            padding: 8px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-bottom: 6px;
        }

        .section-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; font-weight: 700; margin-bottom: 6px; }
        
        .table-pos th { background: var(--vz-table-hover-bg, #f8f9fa); font-weight: 600; color: var(--vz-body-color, #495057); border-bottom-width: 2px; }
        .table-pos td { vertical-align: middle; }

        .select2-container--bootstrap-5 .select2-selection {
            height: 36px !important; min-height: 36px !important; border-radius: 6px; border: 1px solid #ced4da;
            display: flex; align-items: center; font-size: 13px; padding: 4px 8px;
        }
        
        .form-control, .form-select { height: 36px !important; min-height: 36px !important; border-radius: 6px; font-size: 13px; padding: 4px 8px; }
        .btn-sm, .form-control-sm, .form-select-sm { height: 36px !important; min-height: 36px !important; font-size: 13px !important; }

        /* Hide arrows on number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] { -moz-appearance: textfield; }

        /* Beautiful Input */
        .input-beautiful {
            border: 1.5px solid #7f8c8d !important; border-radius: 6px !important; font-size: 14px !important; font-weight: 600 !important; color: #212529 !important; background-color: #f8f9fa !important; transition: all 0.2s ease-in-out !important; box-shadow: inset 0 2px 4px rgba(0,0,0,0.03) !important;
        }
        .input-beautiful:focus { border-color: #0ab39c !important; background-color: #fff !important; outline: none !important; box-shadow: 0 0 0 4px rgba(10, 179, 156, 0.15) !important; }
        .form-control, .form-select, .select2-container--bootstrap-5 .select2-selection { border: 1.5px solid #7f8c8d !important; }
        .form-control:focus, .form-select:focus, .select2-container--bootstrap-5 .select2-selection:focus { border-color: #0ab39c !important; box-shadow: 0 0 0 3px rgba(10, 179, 156, 0.25) !important; }
    </style>
    @endpush

    <div class="row mt-3">
        <div class="col-lg-12">
            <h5 class="mb-3 fw-bold d-flex align-items-center">
                <div class="bg-primary-subtle text-primary p-1 rounded me-2">
                    <i class="ri-arrow-left-right-line fs-18"></i>
                </div>
                New Stock Transfer
            </h5>
            
            <form action="{{ route('stock-transfers.store') }}" method="POST" id="transfer-form">
                @csrf
                <input type="hidden" name="cart" id="cart-data">

                <!-- 1. Select Product -->
                <div class="card-custom">
                    <div class="section-title">1. Select Product</div>
                    
                    <div class="mb-2">
                        <select id="product_search" class="form-select select2" data-placeholder="Search and select a product...">
                            <option value=""></option>
                            @foreach($products as $product)
                                @php $stockQty = $product->stocks->sum('quantity'); @endphp
                                <option value="{{ $product->id }}" 
                                    data-name="{{ addslashes($product->name) }}"
                                    data-stock="{{ $stockQty }}">
                                    {{ $product->name }} (Total Stock: {{ $stockQty }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm table-pos mb-0" id="cart-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th style="width: 220px; min-width: 220px;">Qty to Transfer</th>
                                    <th class="text-center" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <tr id="empty-cart-msg">
                                    <td colspan="3" class="text-center text-muted py-2">
                                        <i class="ri-shopping-basket-line fs-3 display-6 mb-1 d-block text-light"></i>
                                        No products added yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Transfer Details -->
                <div class="card-custom">
                    <div class="section-title">2. Transfer Details</div>
                    
                    <div class="row gx-2 mb-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">From Warehouse</label>
                            <select name="from_warehouse_id" id="from_warehouse_id" class="form-select form-select-sm select2" data-placeholder="Select Source" required>
                                <option value=""></option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">To Warehouse</label>
                            <select name="to_warehouse_id" id="to_warehouse_id" class="form-select form-select-sm select2" data-placeholder="Select Destination" required>
                                <option value=""></option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Transfer Date</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white px-2"><i class="ri-calendar-2-line"></i></span>
                                <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2 text-start">
                        <textarea name="note" class="form-control bg-white" rows="1" placeholder="Add any additional notes or remarks..." style="height: 36px; min-height: 36px;"></textarea>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold fs-14 shadow" id="btn-submit">
                            <i class="ri-arrow-left-right-line align-middle me-1 fs-16"></i> Complete Transfer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let cart = [];

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Product Selection
            $('#product_search').on('select2:select', function (e) {
                const data = e.params.data;
                const opt = $(data.element);
                
                const product = {
                    id: data.id,
                    name: opt.data('name'),
                    stock: opt.data('stock'),
                    qty: 1
                };

                addToCart(product);
                
                // Clear selection
                $(this).val(null).trigger('change');
            });

            // Submit Form
            $('#btn-submit').on('click', function(e) {
                e.preventDefault();
                
                if (cart.length === 0) {
                    Swal.fire('Error', 'Please add at least one product to transfer.', 'error');
                    return;
                }

                if (!$('#from_warehouse_id').val() || !$('#to_warehouse_id').val()) {
                    Swal.fire('Error', 'Please select both source and destination warehouses.', 'error');
                    return;
                }

                if ($('#from_warehouse_id').val() === $('#to_warehouse_id').val()) {
                    Swal.fire('Error', 'Source and destination warehouses cannot be the same.', 'error');
                    return;
                }
                
                // Set cart JSON string
                $('#cart-data').val(JSON.stringify(cart));
                
                // Submit native form
                $('#transfer-form').submit();
            });
        });

        function addToCart(product) {
            const existing = cart.find(item => item.id == product.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push(product);
            }
            renderCart();
        }

        function updateQty(id, qtyStr, isDelta = false) {
            const item = cart.find(item => item.id == id);
            if(item) {
                if (isDelta) {
                    item.qty = Math.max(1, item.qty + parseInt(qtyStr));
                    renderCart();
                } else {
                    let newQty = parseInt(qtyStr);
                    if(isNaN(newQty) || newQty < 1) newQty = 1;
                    item.qty = newQty;
                    
                    const row = $(`tr[data-id="${id}"]`);
                    row.find('.cart-qty').val(item.qty);
                }
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id != id);
            renderCart();
        }

        function renderCart() {
            const tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';

            if(cart.length === 0) {
                tbody.innerHTML = `<tr id="empty-cart-msg"><td colspan="3" class="text-center text-muted py-2">No products added yet.</td></tr>`;
            } else {
                cart.forEach(item => {
                    tbody.innerHTML += `
                        <tr data-id="${item.id}">
                            <td><div class="fw-semibold text-dark">${item.name}</div></td>
                            <td>
                                <div class="d-flex align-items-center mx-auto" style="width: 220px; min-width: 220px;">
                                    <button type="button" class="btn btn-sm btn-light px-2 py-0 border flex-shrink-0" onclick="updateQty('${item.id}', -1, true)"><i class="ri-subtract-line"></i></button>
                                    <input type="number" class="form-control form-control-sm text-center mx-1 px-1 cart-qty input-beautiful" value="${item.qty}" min="1" oninput="updateQty('${item.id}', this.value, false)" style="width: 100%;">
                                    <button type="button" class="btn btn-sm btn-light px-2 py-0 border flex-shrink-0" onclick="updateQty('${item.id}', 1, true)"><i class="ri-add-line"></i></button>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-soft-danger px-2 py-1" onclick="removeFromCart('${item.id}')">
                                    <i class="ri-delete-bin-line fs-14"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
