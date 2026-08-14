<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Create Quotation - POS</title>
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
    <style>
        body { background: #f4f5f8; overflow-x: hidden; font-family: 'Inter', sans-serif; }
        .full-window-modal {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: #f4f5f8; z-index: 9999; display: flex; flex-direction: column;
            animation: slideInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header {
            background: #fff; padding: 15px 30px; border-bottom: 1px solid #e2e5e8;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .modal-body-scroll {
            flex: 1; overflow-y: auto; padding: 30px;
        }
 
        .card-custom {
            background: #fff; border: 1px solid #e2e5e8; border-radius: 12px;
            padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px;
        }
 
        .section-title { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; font-weight: 700; margin-bottom: 16px; }
        
        .table-pos th { background: #f8f9fa; font-weight: 600; color: #495057; border-bottom-width: 2px; }
        .table-pos td { vertical-align: middle; }
 
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px; border-radius: 8px; border: 1px solid #ced4da;
            display: flex; align-items: center;
        }
        
        .form-control { min-height: 42px; border-radius: 8px; }
        
        .net-amount { font-size: 24px; color: #0ab39c; font-weight: 700; }
        
        .btn-close-custom {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; background: #f8d7da;
            color: #dc3545; border: none; transition: 0.2s;
        }
        .btn-close-custom:hover { background: #dc3545; color: #fff; }
 
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
 
        .input-beautiful {
            border: 2px solid #7f8c8d !important;
            border-radius: 8px !important;
            font-size: 16px !important;
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
 
        .select2-container--open .select2-dropdown {
            z-index: 10000 !important;
        }
    </style>
</head>
<body>
 
    <div class="full-window-modal">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="mb-0 fw-bold d-flex align-items-center">
                <div class="bg-primary-subtle text-primary p-2 rounded me-3">
                    <i class="ri-file-text-line fs-20"></i>
                </div>
                Create Quotation
            </h4>
            <button class="btn-close-custom" onclick="closeQuotation()">
                <i class="ri-close-line fs-22"></i>
            </button>
        </div>
 
        <!-- Body -->
        <div class="modal-body-scroll">
            <div class="container-fluid">
                <div class="row">
                    <!-- Top Section: Products -->
                    <div class="col-lg-12">
                        <div class="card-custom">
                            <div class="section-title">1. Select Product</div>
                            
                            <div class="mb-4">
                                <select id="product_search" class="form-select select2" data-placeholder="Search and select a product...">
                                    <option value=""></option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-price="{{ $product->selling_price }}" 
                                            data-name="{{ addslashes($product->name) }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
 
                            <div class="table-responsive" style="min-height: 250px;">
                                <table class="table table-pos mb-0" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th style="width: 120px;">Qty</th>
                                            <th style="width: 180px;">Sale Price</th>
                                            <th>Amount</th>
                                            <th class="text-center" style="width: 60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items">
                                        <tr id="empty-cart-msg">
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="ri-shopping-basket-line fs-1 display-6 mb-3 d-block text-light"></i>
                                                No products added yet. Search and select above.
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
                            <div class="section-title">2. Quotation Details</div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 fw-semibold text-uppercase">Quotation Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="ri-calendar-2-line"></i></span>
                                        <input type="date" id="quotation_date" class="form-control">
                                    </div>
                                </div>
 
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 fw-semibold text-uppercase">Valid Till Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="ri-calendar-event-line"></i></span>
                                        <input type="date" id="valid_till" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 fw-semibold text-uppercase">Customer</label>
                                    <select id="customer_id" class="form-select select2" data-placeholder="Select Customer (Optional)">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fs-12 fw-semibold text-uppercase">Customer Name</label>
                                    <input type="text" id="customer_name" class="form-control" placeholder="Enter customer name (Optional)">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label text-muted fs-12 fw-semibold text-uppercase">Customer Address</label>
                                    <textarea id="address" class="form-control" rows="2" placeholder="Enter customer address (Optional)"></textarea>
                                </div>
                            </div>
                        </div>
 
                        <div class="card-custom bg-light-subtle">
                            <div class="section-title">3. Pricing Summary</div>
                            
                            <div class="row text-center align-items-center mb-3">
                                <div class="col-md-3 border-end">
                                    <span class="totals-label d-block mb-1">Subtotal</span>
                                    <span class="totals-value fs-18" id="subtotal-display">0.00</span>
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <span class="totals-label text-danger d-block mb-2">Discount</span>
                                    <input type="number" id="discount" class="form-control input-beautiful text-center mx-auto" style="max-width: 130px; height: 45px;" value="0" min="0" oninput="calculateTotals()">
                                </div>
                                
                                <div class="col-md-3 border-end">
                                    <span class="totals-label text-primary d-block mb-2">Delivery Charges</span>
                                    <input type="number" id="delivery_charges" class="form-control input-beautiful text-center mx-auto" style="max-width: 130px; height: 45px;" value="0" min="0" oninput="calculateTotals()">
                                </div>
                                
                                <div class="col-md-3">
                                    <span class="fw-bold fs-16 d-block mb-1">Net Amount</span>
                                    <span class="net-amount" id="net-amount-display">0.00</span>
                                </div>
                            </div>
 
                            <!-- Notes -->
                            <div class="mt-4 mb-4 text-start">
                                <div class="section-title">4. Quotation Note</div>
                                <textarea id="note" class="form-control bg-white" rows="2" placeholder="Add any notes, remarks or payment terms for this quotation..."></textarea>
                            </div>
 
                            <div class="mt-4">
                                <button type="button" class="btn btn-success w-100 py-3 fw-bold fs-16 shadow" id="btn-submit">
                                    <i class="ri-check-double-line align-middle me-2 fs-20"></i> Create Quotation
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
        let cart = [];
 
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

            document.getElementById('quotation_date').setAttribute('max', localDateStr);
            document.getElementById('quotation_date').value = localDateStr;

            // Set default valid_till to today + 7 days
            const validTillDate = new Date();
            validTillDate.setDate(localToday.getDate() + 7);
            const vtYear = validTillDate.getFullYear();
            const vtMonth = String(validTillDate.getMonth() + 1).padStart(2, '0');
            const vtDay = String(validTillDate.getDate()).padStart(2, '0');
            const validTillStr = `${vtYear}-${vtMonth}-${vtDay}`;

            document.getElementById('valid_till').setAttribute('min', localDateStr);
            document.getElementById('valid_till').value = validTillStr;

            // Customer selection auto-fill
            $('#customer_id').on('change', function() {
                const opt = $(this).find(':selected');
                if (opt.val()) {
                    $('#customer_name').val(opt.text());
                    $('#address').val(opt.data('address') || '');
                } else {
                    $('#customer_name').val('');
                    $('#address').val('');
                }
            });

            // Handle submission click listener
            $('#btn-submit').on('click', function(e) {
                e.preventDefault();
                processQuotation();
            });
 
            $('#product_search').on('select2:select', function (e) {
                const data = e.params.data;
                const opt = $(data.element);
                const id = data.id;
                
                if(!id) return;
 
                const name = opt.data('name');
                const price = parseFloat(opt.data('price'));
 
                addToCart(id, name, price);
                $(this).val(null).trigger('change');
            });
        });
 
        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id == id);
            if(existing) {
                existing.qty++;
            } else {
                cart.push({ id, name, price, qty: 1 });
            }
            renderCart();
        }
 
        function updateQty(id, qty) {
            const item = cart.find(item => item.id == id);
            if(item) {
                let newQty = parseInt(qty);
                if(isNaN(newQty) || newQty < 1) newQty = 1;
                item.qty = newQty;
                
                // Update row subtotal cell and calculate totals live
                const row = document.querySelector(`input[oninput*="updateQty('${id}'"]`)?.closest('tr');
                if (row) {
                    const amtCell = row.querySelector('.item-amount');
                    if (amtCell) amtCell.innerText = (item.price * item.qty).toFixed(2);
                }
                calculateTotals();
            }
        }

        function updatePrice(id, price) {
            const item = cart.find(item => item.id == id);
            if(item) {
                let newPrice = parseFloat(price);
                if(isNaN(newPrice) || newPrice < 0) newPrice = 0;
                item.price = newPrice;
                
                // Update row subtotal cell and calculate totals live
                const row = document.querySelector(`input[oninput*="updatePrice('${id}'"]`)?.closest('tr');
                if (row) {
                    const amtCell = row.querySelector('.item-amount');
                    if (amtCell) amtCell.innerText = (item.price * item.qty).toFixed(2);
                }
                calculateTotals();
            }
        }
 
        function removeItem(id) {
            cart = cart.filter(item => item.id != id);
            renderCart();
        }
 
        function renderCart() {
            const tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';
 
            if(cart.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-cart-msg">
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="ri-shopping-basket-line fs-1 display-6 mb-3 d-block text-light"></i>
                            No products added yet. Search and select above.
                        </td>
                    </tr>
                `;
            } else {
                cart.forEach(item => {
                    const amount = item.price * item.qty;
                    tbody.innerHTML += `
                        <tr>
                            <td class="fw-medium">${item.name}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-center" style="width: 80px;" value="${item.qty}" min="1" oninput="updateQty('${item.id}', this.value)">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-center" style="width: 120px;" value="${item.price}" min="0" step="0.01" oninput="updatePrice('${item.id}', this.value)">
                            </td>
                            <td class="fw-bold item-amount">${amount.toFixed(2)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-soft-danger" onclick="removeItem('${item.id}')">
                                    <i class="ri-close-fill fs-16"></i>
                                </button>
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
        }
 
        function processQuotation() {
            if(cart.length === 0) {
                Swal.fire('Empty Cart', 'Please add products to the quotation cart first.', 'warning');
                return;
            }
 
            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line ri-spin align-middle me-2 fs-20"></i> Creating...';
 
            const payload = {
                _token: '{{ csrf_token() }}',
                customer_id: document.getElementById('customer_id').value || null,
                customer_name: document.getElementById('customer_name').value || null,
                address: document.getElementById('address').value || null,
                date: document.getElementById('quotation_date').value,
                valid_till: document.getElementById('valid_till').value || null,
                discount: document.getElementById('discount').value || 0,
                delivery_charges: document.getElementById('delivery_charges').value || 0,
                note: document.getElementById('note').value,
                products: cart.map(i => ({ id: i.id, quantity: i.qty, unit_price: i.price }))
            };
 
            axios.post('{{ route('quotations.store') }}', payload)
                .then(res => {
                    if(res.data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Quotation created successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (window !== window.parent && typeof window.parent.onEditSuccess === 'function') {
                                window.parent.onEditSuccess();
                            } else {
                                window.location.href = '/quotations';
                            }
                        });
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-check-double-line align-middle me-2 fs-20"></i> Create Quotation';
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

        function closeQuotation() {
            if (window !== window.parent && typeof window.parent.closeEditModal === 'function') {
                window.parent.closeEditModal();
            } else {
                window.location.href = "{{ route('quotations.index') }}";
            }
        }
    </script>
</body>
</html>
