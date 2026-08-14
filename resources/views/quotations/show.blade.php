<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Quotation #QT-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</title>
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
                    <i class="ri-file-text-line fs-20"></i>
                </div>
                Quotation #QT-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}
            </h4>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-secondary d-print-none" onclick="window.print()"><i class="ri-printer-line align-middle me-1"></i> Print Quotation</button>
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
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-header border-bottom-dashed p-4">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <img src="{{ asset('assets/images/logo-dark.png') }}" class="card-logo card-logo-dark" alt="logo dark" height="17">
                                                <div class="mt-sm-5 mt-4">
                                                    <h6 class="text-muted text-uppercase fw-semibold">Address</h6>
                                                    <p class="text-muted mb-1">{{ $quotation->location->address ?? 'Your Store Address' }}</p>
                                                    <p class="text-muted mb-0"><span>Phone:</span><span id="contact-no"> {{ $quotation->location->phone ?? '+123456789' }}</span></p>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 mt-sm-0 mt-3">
                                                <h6><span class="text-muted fw-normal">Quotation No:</span> <span id="legal-register-no">QT-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</span></h6>
                                                <h6><span class="text-muted fw-normal">Date:</span> <span id="email">{{ \Carbon\Carbon::parse($quotation->date)->format('d M, Y') }}</span></h6>
                                                @if($quotation->valid_till)
                                                <h6><span class="text-muted fw-normal">Valid Till:</span> <span>{{ \Carbon\Carbon::parse($quotation->valid_till)->format('d M, Y') }}</span></h6>
                                                @endif
                                                <h6 class="mb-0"><span class="text-muted fw-normal">Salesperson:</span> <span id="contact-no">{{ $quotation->user->name ?? 'System' }}</span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            <div class="col-lg-4 col-6">
                                                <p class="text-muted mb-2 text-uppercase fw-semibold">Customer Details</p>
                                                <h5 class="fs-14 mb-0"><span id="invoice-no">{{ $quotation->customer_name ?? $quotation->customer->name ?? 'Walk-in Customer' }}</span></h5>
                                                @if($quotation->customer)
                                                    <p class="text-muted mb-1">{{ $quotation->customer->phone }}</p>
                                                @endif
                                                @if($quotation->address)
                                                    <p class="text-muted mb-0">{{ $quotation->address }}</p>
                                                @elseif($quotation->customer && $quotation->customer->address)
                                                    <p class="text-muted mb-0">{{ $quotation->customer->address }}</p>
                                                @endif
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <p class="text-muted mb-2 text-uppercase fw-semibold">Status</p>
                                                @if($quotation->status === 'converted')
                                                    <span class="badge bg-success-subtle text-success fs-11" id="payment-status">Converted to Sale</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning fs-11" id="payment-status">Pending / Sent</span>
                                                @endif
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <p class="text-muted mb-2 text-uppercase fw-semibold">Total Amount</p>
                                                <h5 class="fs-14 mb-0"><span id="total-amount">{{ number_format($quotation->net_amount, 2) }}</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card-body p-4 border-top border-top-dashed">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                                <thead>
                                                    <tr class="table-active">
                                                        <th scope="col" style="width: 50px;">#</th>
                                                        <th scope="col" class="text-start">Product Details</th>
                                                        <th scope="col">Rate</th>
                                                        <th scope="col">Quantity</th>
                                                        <th scope="col" class="text-end">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="products-list">
                                                    @foreach($quotation->items as $index => $item)
                                                    <tr>
                                                        <th scope="row">{{ $index + 1 }}</th>
                                                        <td class="text-start">
                                                            <span class="fw-medium">{{ $item->product->name ?? 'Unknown' }}</span>
                                                            <p class="text-muted mb-0">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                                                        </td>
                                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="border-top border-top-dashed mt-2">
                                            <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                                <tbody>
                                                    <tr>
                                                        <td>Sub Total</td>
                                                        <td class="text-end">{{ number_format($quotation->total_amount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Discount</td>
                                                        <td class="text-end text-danger">- {{ number_format($quotation->discount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Delivery Charges</td>
                                                        <td class="text-end text-primary">+ {{ number_format($quotation->delivery_charges, 2) }}</td>
                                                    </tr>
                                                    <tr class="border-top border-top-dashed fs-15">
                                                        <th scope="row">Net Amount</th>
                                                        <th class="text-end">{{ number_format($quotation->net_amount, 2) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($quotation->note)
                                        <div class="mt-4">
                                            <p class="text-muted mb-1 text-uppercase fw-semibold">Note / Terms</p>
                                            <p class="text-muted">{{ $quotation->note }}</p>
                                        </div>
                                        @endif
                                        <div class="mt-4 text-center">
                                            <p class="text-muted mb-0">Thank you for requesting a quotation. This document is valid till the date specified above.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                window.location.href = "{{ route('quotations.index') }}";
            }
        }
    </script>
</body>
</html>
