<x-app-layout>
    <div class="row mt-3">
        <div class="col-lg-8 offset-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 pb-3">
                    <h5 class="card-title mb-0">General Settings</h5>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-medium">Application Logo</label>
                            <p class="text-muted small mb-2">Upload a logo to appear in the sidebar. Recommended size: 200x50px.</p>
                            @if(isset($settings['logo']))
                                <div class="mb-3 p-3 bg-light rounded text-center border" style="max-width: 250px;">
                                    <img src="{{ asset($settings['logo']) }}" alt="Current Logo" class="img-fluid" style="max-height: 50px;">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                                    <label class="form-check-label text-danger" for="removeLogo">
                                        Remove current logo image
                                    </label>
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Logo Text (Alternative to Image)</label>
                            <p class="text-muted small mb-2">If you don't upload a logo, this text will be displayed in the sidebar instead.</p>
                            <input type="text" name="logo_text" class="form-control" value="{{ $settings['logo_text'] ?? '' }}" placeholder="Enter logo text (e.g. My POS)">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Invoice Banner</label>
                            <p class="text-muted small mb-2">Upload a banner to appear at the top of Sales and Purchase invoices. Recommended size: 800x150px.</p>
                            @if(isset($settings['banner']))
                                <div class="mb-3 p-3 bg-light rounded text-center border">
                                    <img src="{{ asset($settings['banner']) }}" alt="Current Banner" class="img-fluid" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" name="banner" class="form-control" accept="image/*">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ri-save-line align-bottom me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
