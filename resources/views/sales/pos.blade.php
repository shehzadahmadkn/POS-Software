<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Create Sale - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Custom Styles -->
    <style>
        body { background: #f4f5f8; overflow-x: hidden; font-family: 'Inter', sans-serif; }
        .full-window-modal {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: #f4f5f8; z-index: 9999; display: flex; flex-direction: column;
            animation: slideInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header {
            background: #fff; padding: 10px 20px; border-bottom: 1px solid #e2e5e8;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .modal-body-scroll {
            flex: 1; overflow-y: auto; padding: 6px 12px;
        }

        .card-custom {
            background: #fff; border: 1px solid #e2e5e8; border-radius: 8px;
            padding: 8px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-bottom: 6px;
        }

        .section-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; font-weight: 700; margin-bottom: 6px; }
        
        .table-pos th { background: #f8f9fa; font-weight: 600; color: #495057; border-bottom-width: 2px; }
        .table-pos td { vertical-align: middle; }

        .select2-container--bootstrap-5 .select2-selection {
            height: 36px !important; min-height: 36px !important; border-radius: 6px; border: 1px solid #ced4da;
            display: flex; align-items: center; font-size: 13px; padding: 4px 8px;
        }
        
        .form-control, .form-select { height: 36px !important; min-height: 36px !important; border-radius: 6px; font-size: 13px; padding: 4px 8px; }
        .btn-sm, .form-control-sm, .form-select-sm { height: 36px !important; min-height: 36px !important; font-size: 13px !important; }
        
        .totals-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px dashed #e2e5e8; }
        .totals-row:last-child { border-bottom: none; }
        .totals-label { color: #6c757d; font-weight: 500; font-size: 12px; }
        .totals-value { font-weight: 600; font-size: 14px; }
        
        .net-amount { font-size: 20px; color: #0ab39c; font-weight: 700; }
        
        .btn-close-custom {
            width: 32px; height: 32px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; background: #f8d7da;
            color: #dc3545; border: none; transition: 0.2s;
        }
        .btn-close-custom:hover { background: #dc3545; color: #fff; }

        /* Hide arrows on number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Beautiful Input */
        .input-beautiful {
            border: 1.5px solid #7f8c8d !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #212529 !important;
            background-color: #f8f9fa !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.03) !important;
        }
        .input-beautiful:focus {
            border-color: #0ab39c !important;
            background-color: #fff !important;
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(10, 179, 156, 0.15) !important;
        }

        /* Make standard form controls, select, and Select2 borders highly visible */
        .form-control, .form-select, .select2-container--bootstrap-5 .select2-selection {
            border: 1.5px solid #7f8c8d !important;
        }
        .form-control:focus, .form-select:focus, .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: #0ab39c !important;
            box-shadow: 0 0 0 3px rgba(10, 179, 156, 0.25) !important;
        }

        /* Fix Select2 z-index to show above full-window-modal */
        .select2-container--open .select2-dropdown {
            z-index: 10000 !important;
        }
    </style>
</head>
<body>

    <div class="full-window-modal">
        <!-- Header -->
        <div class="modal-header">
            <h5 class="mb-0 fw-bold d-flex align-items-center">
                <div class="bg-primary-subtle text-primary p-1 rounded me-2">
                    <i class="ri-shopping-cart-2-line fs-18"></i>
                </div>
                {{ isset($sale) ? 'Edit Sale' : 'Create Sale' }}
            </h5>
            <button class="btn-close-custom" onclick="closePOS()">
                <i class="ri-close-line fs-22"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="modal-body-scroll">
            <div class="container-fluid px-0">
                <div class="row">
                    <!-- Top Section: Products -->
                    <div class="col-lg-12">
                        <div class="card-custom">
                            <div class="section-title">1. Select Product</div>
                            
                            <div class="mb-2">
                                <select id="product_search" class="form-select select2" data-placeholder="Search and select a product...">
                                    <option value=""></option>
                                    @foreach($products as $product)
                                        @php $stockQty = $product->stocks->first() ? $product->stocks->first()->quantity : 0; @endphp
                                        <option value="{{ $product->id }}" 
                                            data-price="{{ $product->selling_price }}" 
                                            data-stock="{{ $stockQty }}"
                                            data-name="{{ addslashes($product->name) }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                <table class="table table-sm table-pos mb-0" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Warehouse</th>
                                            <th style="width: 90px;">Qty</th>
                                            <th style="width: 110px;">Sale Price</th>
                                            <th>Amount</th>
                                            <th class="text-center" style="width: 50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items">
                                        <!-- Items injected via JS -->
                                        <tr id="empty-cart-msg">
                                            <td colspan="6" class="text-center text-muted py-2">
                                                <i class="ri-shopping-basket-line fs-3 display-6 mb-1 d-block text-light"></i>
                                                No products added yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Section: Settings & Totals -->
                    <div class="col-lg-12">
                        <div class="card-custom">
                            <div class="section-title">2. Sale Details & Payment</div>
                            
                            <div class="row gx-2 mb-2 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Date</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white px-2"><i class="ri-calendar-2-line"></i></span>
                                        <input type="date" id="transaction_date" class="form-control" value="{{ isset($sale) ? $sale->transaction_date : date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Customer</label>
                                    <select id="customer_id" class="form-select form-select-sm select2" data-placeholder="Select Customer">
                                        <option value="">Walk-in</option>
                                        <optgroup label="Customers">
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ isset($sale) && $sale->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Group Accounts">
                                            @foreach($groupAccounts as $group)
                                                <option value="{{ $group->customer_id }}" {{ isset($sale) && $sale->customer_id == $group->customer_id ? 'selected' : '' }}>Group: {{ $group->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Account</label>
                                    <select id="account_id" class="form-select form-select-sm select2" data-placeholder="Select Account">
                                        <option value=""></option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ isset($sale) && $sale->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Status</label>
                                    <select id="payment_status" class="form-select form-select-sm select2" onchange="calculateTotals()">
                                        <option value="paid" {{ isset($sale) && $sale->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="pending" {{ isset($sale) && $sale->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="partial" {{ isset($sale) && $sale->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="advance" {{ isset($sale) && $sale->payment_status == 'advance' ? 'selected' : '' }}>Advance</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted fs-11 fw-semibold text-uppercase mb-1">Paid Amt</label>
                                    <input type="number" id="paid_amount" class="form-control form-control-sm" value="{{ isset($sale) ? $sale->paid_amount : '0' }}" min="0" step="0.01" oninput="calculateTotals()">
                                </div>
                            </div>
                            
                            <hr class="my-2" style="border-top: 1px dashed #e2e5e8;">

                            <div class="row text-center align-items-center mb-2">
                                <div class="col-md-3 border-end">
                                    <span class="totals-label d-block mb-1">Subtotal</span>
                                    <span class="totals-value fs-16" id="subtotal-display">0.00</span>
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <span class="totals-label text-danger d-block mb-1">Discount</span>
                                    <input type="number" id="discount" class="form-control form-control-sm input-beautiful text-center mx-auto" style="max-width: 180px;" value="{{ isset($sale) ? $sale->discount : '0' }}" min="0" oninput="calculateTotals()">
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <span class="totals-label text-warning d-block mb-1">Delivery</span>
                                    <input type="number" id="delivery_charges" class="form-control form-control-sm input-beautiful text-center mx-auto" style="max-width: 180px;" value="{{ isset($sale) ? $sale->delivery_charges : '0' }}" min="0" oninput="calculateTotals()">
                                </div>
                                
                                <div class="col-md-3">
                                    <span class="fw-bold fs-14 d-block mb-1">Net Amount</span>
                                    <span class="net-amount fs-20" id="net-amount-display">0.00</span>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mt-2 mb-2 text-start">
                                <textarea id="note" class="form-control bg-white" rows="1" placeholder="Add any additional notes or remarks..." style="height: 36px; min-height: 36px;">{{ isset($sale) ? $sale->note : '' }}</textarea>
                            </div>

                            <div class="mt-2">
                                <button type="button" class="btn btn-success w-100 py-1 fw-bold fs-14 shadow" id="btn-submit">
                                    <i class="ri-check-double-line align-middle me-1 fs-16"></i> {{ isset($sale) ? 'Update Sale' : 'Create Sale' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.onerror = function(message, source, lineno, colno, error) {
            alert("JS Error: " + message + " on line " + lineno + "\nSource: " + source);
            return false;
        };
        if (typeof Swal === 'undefined') {
            window.Swal = {
                fire: function(title, text, icon) {
                    alert(title + "\n\n" + text);
                    return {
                        then: function(callback) {
                            if (callback) callback();
                            return this;
                        }
                    };
                }
            };
        }
        
        // Store warehouses globally to generate selects
        const warehouseOptions = @json($warehouses->map(function($w) { return ['id' => $w->id, 'name' => $w->name]; })->toArray());

        let cart = [];

        @if(isset($sale))
            let saleItems = @json($sale->items);
            saleItems.forEach(item => {
                cart.push({
                    id: item.product_id,
                    name: item.product.name,
                    price: parseFloat(item.unit_price),
                    stock: 9999, // Bypass stock limit for editing
                    qty: item.quantity,
                    warehouse_id: item.warehouse_id
                });
            });
            renderCart();
        @endif

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Restrict future dates and set default to local date
            const localToday = new Date();
            const year = localToday.getFullYear();
            const month = String(localToday.getMonth() + 1).padStart(2, '0');
            const day = String(localToday.getDate()).padStart(2, '0');
            const localDateStr = `${year}-${month}-${day}`;

            document.getElementById('transaction_date').setAttribute('max', localDateStr);
            @if(!isset($sale))
                document.getElementById('transaction_date').value = localDateStr;
            @endif

            // Initialize total states
            calculateTotals();

            // Handle submission click listener
            $('#btn-submit').on('click', function(e) {
                e.preventDefault();
                processSale();
            });

            // Handle product selection
            window.updateCartWarehouse = function(id, warehouse_id) {
                let item = cart.find(i => i.id == id);
                if(item) {
                    item.warehouse_id = warehouse_id;
                }
            }

            $('#product_search').on('select2:select', function (e) {
                var data = e.params.data;
                var el = $(data.element);
                
                var id = data.id;
                var name = el.data('name');
                var price = parseFloat(el.data('price'));
                var stock = parseInt(el.data('stock'));

                let existing = cart.find(i => i.id == id);
                if (existing) {
                    existing.qty++;
                    renderCart();
                } else {
                    let defaultWarehouseId = warehouseOptions.length > 0 ? warehouseOptions[0].id : '';
                    cart.push({
                        id: id,
                        name: name,
                        price: price,
                        stock: stock,
                        qty: 1,
                        warehouse_id: defaultWarehouseId
                    });
                    renderCart();
                }
                $(this).val(null).trigger('change');
            });
        });

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
                    const amount = item.qty * item.price;
                    row.find('.cart-amount').val(amount.toFixed(2));
                    calculateTotals();
                }
            }
        }

        function updatePrice(id, price) {
            const item = cart.find(item => item.id == id);
            if(item) {
                item.price = parseFloat(price) || 0;
                
                const row = $(`tr[data-id="${id}"]`);
                let amount = item.price * item.qty;
                row.find('.cart-amount').val(amount.toFixed(2));
                
                calculateTotals();
            }
        }

        function updateAmount(id, amount) {
            const item = cart.find(item => item.id == id);
            if(item) {
                let newAmount = parseFloat(amount) || 0;
                if (item.qty > 0) {
                    // Calculate Price instead of Qty
                    item.price = newAmount / item.qty;
                }
                
                const row = $(`tr[data-id="${id}"]`);
                row.find('.cart-price').val(item.price.toFixed(2));
                calculateTotals();
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
                tbody.innerHTML = `
                    <tr id="empty-cart-msg">
                        <td colspan="6" class="text-center text-muted py-2">
                            <i class="ri-shopping-basket-line fs-3 display-6 mb-1 d-block text-light"></i>
                            No products added yet.
                        </td>
                    </tr>
                `;
            } else {
                cart.forEach(item => {
                    const amount = item.price * item.qty;
                    let wOptions = `<option value="">Select...</option>`;
                    warehouseOptions.forEach(function(w) {
                        let selected = (item.warehouse_id == w.id) ? 'selected' : '';
                        wOptions += `<option value="${w.id}" ${selected}>${w.name}</option>`;
                    });

                    tbody.innerHTML += `
                        <tr data-id="${item.id}">
                            <td>
                                <div class="fw-semibold text-dark text-truncate">${item.name}</div>
                            </td>
                            <td>
                                <select class="form-select form-select-sm cart-warehouse" onchange="updateCartWarehouse('${item.id}', this.value)" style="min-width: 180px; max-width: 180px; padding: 0.25rem 1.5rem 0.25rem 0.5rem;">
                                    ${wOptions}
                                </select>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="width: 180px;">
                                    <button class="btn btn-sm btn-light px-2 py-0 border flex-shrink-0" onclick="updateQty('${item.id}', -1, true)"><i class="ri-subtract-line"></i></button>
                                    <input type="number" class="form-control form-control-sm text-center mx-1 px-1 cart-qty" value="${item.qty}" min="1" oninput="updateQty('${item.id}', this.value, false)" style="width: 100%;">
                                    <button class="btn btn-sm btn-light px-2 py-0 border flex-shrink-0" onclick="updateQty('${item.id}', 1, true)"><i class="ri-add-line"></i></button>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-end px-1 cart-price" value="${item.price.toFixed(2)}" oninput="updatePrice('${item.id}', this.value)" style="width: 180px;">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-end px-1 fw-bold cart-amount" value="${amount.toFixed(2)}" oninput="updateAmount('${item.id}', this.value)" style="width: 180px;">
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-soft-danger px-2 py-1" onclick="removeFromCart('${item.id}')"><i class="ri-delete-bin-line"></i></button>
                            </td>
                        </tr>
                    `;
                });
            }
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            cart.forEach(item => { subtotal += item.price * item.qty; });
            
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const delivery = parseFloat(document.getElementById('delivery_charges').value) || 0;
            
            const netAmount = Math.max(0, subtotal - discount) + delivery;

            document.getElementById('subtotal-display').innerText = subtotal.toFixed(2);
            document.getElementById('net-amount-display').innerText = netAmount.toFixed(2);

            const status = document.getElementById('payment_status').value;
            const paidInput = document.getElementById('paid_amount');

            if (status === 'paid' || status === 'advance') {
                paidInput.value = netAmount.toFixed(2);
                paidInput.readOnly = true;
            } else if (status === 'pending') {
                paidInput.value = '0.00';
                paidInput.readOnly = true;
            } else {
                paidInput.readOnly = false;
            }
        }

        function processSale() {
            if(cart.length === 0) {
                Swal.fire('Empty Cart', 'Please add products to the cart first.', 'warning');
                return;
            }

            let cartData = cart.map(item => ({
                id: item.id,
                quantity: item.qty,
                unit_price: item.price,
                warehouse_id: item.warehouse_id
            }));

            // Check if all items have a warehouse assigned
            let missingWarehouse = cartData.some(i => !i.warehouse_id);
            if(missingWarehouse) {
                Swal.fire({icon: 'error', title: 'Validation Error', text: 'Please select a warehouse for all products in the cart.'});
                return;
            }

            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line ri-spin align-middle me-2 fs-20"></i> Processing...';

            const payload = {
                _token: '{{ csrf_token() }}',
                customer_id: $('#customer_id').val(),
                account_id: $('#account_id').val(),
                transaction_date: $('#transaction_date').val(),
                payment_status: $('#payment_status').val(),
                discount: $('#discount').val(),
                delivery_charges: $('#delivery_charges').val(),
                note: $('#note').val(),
                paid_amount: $('#paid_amount').val(),
                products: cartData
            };

            const isEdit = {{ isset($sale) ? 'true' : 'false' }};
            const url = isEdit ? '/sales/{{ isset($sale) ? $sale->id : 0 }}' : '{{ route('sales.store') }}';
            const method = isEdit ? 'put' : 'post';

            axios({
                method: method,
                url: url,
                data: payload
            })
                .then(res => {
                    if(res.data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: isEdit ? 'Sale updated successfully!' : 'Sale created successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (window !== window.parent && typeof window.parent.onEditSuccess === 'function') {
                                window.top.location.href = '/sales/' + res.data.sale_id;
                            } else {
                                window.location.href = '/sales/' + res.data.sale_id;
                            }
                        });
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = isEdit ? '<i class="ri-check-double-line align-middle me-2 fs-20"></i> Update Sale' : '<i class="ri-check-double-line align-middle me-2 fs-20"></i> Create Sale';
                    console.error(err);
                    let msg = 'An error occurred';
                    if (err.response && err.response.data) {
                        if (err.response.data.message) {
                            msg = err.response.data.message;
                        } else if (err.response.data.errors) {
                            msg = Object.values(err.response.data.errors).flat().join('\n');
                        }
                    } else if (err.message) {
                        msg = err.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }

        function closePOS() {
            if (window !== window.parent && typeof window.parent.closeEditModal === 'function') {
                window.parent.closeEditModal();
            } else {
                window.location.href = "{{ route('sales.index') }}";
            }
        }
    </script>
</body>
</html>
