<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Invoice #INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</title>
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
    <!-- Custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body { background: var(--vz-body-bg, #f4f5f8); overflow-x: hidden; font-family: 'Outfit', 'Inter', sans-serif; color: var(--vz-body-color, #212529); }
        .full-window-modal {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: var(--vz-body-bg, #f4f5f8); z-index: 9999; display: flex; flex-direction: column;
            color: var(--vz-body-color, #212529);
            animation: slideInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header {
            background: var(--vz-card-bg, #fff); padding: 15px 30px; border-bottom: 1px solid var(--vz-border-color, #e2e5e8);
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .modal-body-scroll {
            flex: 1; overflow-y: auto; padding: 30px;
        }

        .btn-close-custom {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; background: #f8d7da;
            color: #dc3545; border: none; transition: 0.2s;
        }
        .btn-close-custom:hover { background: #dc3545; color: #fff; }

        @page { size: auto; margin: 10px; }
        @media print {
            html, body, #demo, .card, .table, td, th, tr, div, p, span, h1, h4, h5, h6 {
                background-color: #ffffff !important;
                background: #ffffff !important;
                color: #000000 !important;
            }
            body * {
                visibility: hidden;
            }
            #demo, #demo * {
                visibility: visible;
            }
            #demo {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .d-print-none {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="full-window-modal">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="mb-0 fw-bold d-flex align-items-center">
                <div class="bg-primary-subtle text-primary p-2 rounded me-3">
                    <i class="ri-bill-line fs-20"></i>
                </div>
                Invoice #INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
            </h4>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-secondary d-print-none" onclick="window.print()"><i class="ri-printer-line align-middle me-1"></i> Print Invoice</button>
                <button class="btn-close-custom d-print-none" onclick="closeView()">
                    <i class="ri-close-line fs-22"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="modal-body-scroll">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xxl-9">
                        <div class="card" id="demo">
                            @if(\App\Models\Setting::getSetting('banner'))
                            <div class="text-center bg-light">
                                <img src="{{ asset(\App\Models\Setting::getSetting('banner')) }}" alt="Invoice Banner" class="img-fluid" style="width: 100%; height: auto;">
                            </div>
                            @endif
                            <div class="card-body p-4">
                                <h1 class="text-center mb-4 mt-2 text-uppercase" style="font-size: 2.0rem; font-weight: 800; letter-spacing: 1px;">Sales Invoice</h1>
                                
                                <div class="row mb-4">
                                    <div class="col-4 text-center border-end">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-1">Invoice #</h6>
                                        <p class="fs-15 mb-0">INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    <div class="col-4 text-center border-end">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-1">Customer</h6>
                                        <p class="fs-15 mb-0 fw-bold">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-1">Date</h6>
                                        <p class="fs-15 mb-0">{{ \Carbon\Carbon::parse($sale->transaction_date)->format('d M, Y') }}</p>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered text-center align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Product</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Price</th>
                                                <th scope="col" class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->items as $index => $item)
                                            <tr>
                                                <th scope="row">{{ $index + 1 }}</th>
                                                <td class="text-start fw-medium">{{ $item->product->name ?? 'Unknown' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-lg-5 col-md-6 col-sm-8">
                                        @php
                                            $pending = max(0, $sale->net_amount - $sale->paid_amount);
                                            $previousBalance = $sale->customer ? $sale->customer->balance - $pending : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Discount:</span>
                                            <span class="text-danger">- {{ number_format($sale->discount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Delivery:</span>
                                            <span>+ {{ number_format($sale->delivery_charges, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 fs-15 fw-bold border-bottom pb-2">
                                            <span>Total Bill:</span>
                                            <span>{{ number_format($sale->net_amount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Paid:</span>
                                            <span class="text-success">{{ number_format($sale->paid_amount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 text-danger">
                                            <span>Pending (This Bill):</span>
                                            <span>{{ number_format($pending, 2) }}</span>
                                        </div>
                                        
                                        @if($sale->customer)
                                        <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-14">
                                            <span class="text-muted">Previous Balance:</span>
                                            <span>{{ number_format($previousBalance, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between bg-light p-2 rounded mt-2 fs-15 fw-bold text-primary">
                                            <span>Total Balance:</span>
                                            <span>{{ number_format($sale->customer->balance, 2) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                @if($sale->note)
                                <div class="mt-4 border-top pt-3">
                                    <p class="text-muted mb-1 text-uppercase fw-semibold">Notes</p>
                                    <p class="mb-0">{{ $sale->note }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function closeView() {
            if (window !== window.parent && typeof window.parent.closeEditModal === 'function') {
                window.parent.closeEditModal();
            } else {
                window.location.href = "{{ route('sales.index') }}";
            }
        }
    </script>
</body>
</html>
