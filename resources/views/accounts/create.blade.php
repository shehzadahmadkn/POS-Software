<x-app-layout>
    <div class="row mt-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0 fw-bold">Create Account</h4>
                </div>
                <div class="card-body p-4">
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

                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Account Title</label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter account title or name">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" id="accountType" class="form-select" required onchange="toggleSections()">
                                <option value="" disabled selected>Select Type</option>
                                <option value="Business">Business</option>
                                <option value="Customer">Customer</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                        </div>

                        <!-- Business Section -->
                        <div id="businessSection" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                            <h6 class="fw-bold mb-3 text-primary"><i class="ri-bank-line align-middle me-1"></i> Business Details</h6>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" id="businessCategory">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label">Initial Amount</label>
                                    <input type="number" name="initial_amount" id="businessAmount" class="form-control" min="0" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select name="balance_type" id="businessBalanceType" class="form-select">
                                        <option value="debit">Debit (+)</option>
                                        <option value="credit">Credit (-)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Customer/Vendor Section -->
                        <div id="cvSection" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                            <h6 class="fw-bold mb-3 text-success" id="cvHeader"><i class="ri-user-line align-middle me-1"></i> Details</h6>
                            <div class="mb-3">
                                <label class="form-label">Contact #</label>
                                <input type="text" name="phone" class="form-control" placeholder="Phone Number">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Full Address">
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label">Initial Amount</label>
                                    <input type="number" name="initial_amount" id="cvAmount" class="form-control" min="0" step="0.01" placeholder="0.00" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select name="balance_type_cv" id="cvBalanceType" class="form-select" disabled>
                                        <option value="debit">Debit (+)</option>
                                        <option value="credit">Credit (-)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" id="btnSubmit" disabled>
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleSections() {
            const type = document.getElementById('accountType').value;
            const bSec = document.getElementById('businessSection');
            const cvSec = document.getElementById('cvSection');
            const btnSubmit = document.getElementById('btnSubmit');

            // Reset disabled states for initial amount inputs to prevent submitting both
            document.getElementById('businessAmount').disabled = true;
            document.getElementById('cvAmount').disabled = true;
            document.getElementById('businessCategory').disabled = true;
            document.getElementById('businessBalanceType').disabled = true;
            document.getElementById('cvBalanceType').disabled = true;

            btnSubmit.disabled = false;

            if (type === 'Business') {
                bSec.style.display = 'block';
                cvSec.style.display = 'none';
                
                document.getElementById('businessAmount').disabled = false;
                document.getElementById('businessCategory').disabled = false;
                document.getElementById('businessBalanceType').disabled = false;
            } else if (type === 'Customer' || type === 'Vendor') {
                bSec.style.display = 'none';
                cvSec.style.display = 'block';
                
                document.getElementById('cvHeader').innerHTML = `<i class="ri-user-line align-middle me-1"></i> ${type} Details`;
                
                document.getElementById('cvAmount').disabled = false;
                document.getElementById('cvBalanceType').disabled = false;
            } else {
                bSec.style.display = 'none';
                cvSec.style.display = 'none';
                btnSubmit.disabled = true;
            }
        }
    </script>
    @endpush
</x-app-layout>
