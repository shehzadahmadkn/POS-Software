<x-app-layout>
    <x-slot name="header">
        <div class="row mb-3 pb-1">
            <div class="col-12">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-16 mb-1">Edit Product</h4>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <a href="{{ route('products.index') }}" class="btn btn-primary"><i class="ri-arrow-left-line align-bottom me-1"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.update', $product->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Product Name:</label>
                                <input type="text" name="name" class="form-control" placeholder="Product Name" value="{{ $product->name }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU:</label>
                                <input type="text" name="sku" class="form-control" placeholder="SKU" value="{{ $product->sku }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode:</label>
                                <input type="text" name="barcode" class="form-control" placeholder="Barcode" value="{{ $product->barcode }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" step="0.01" name="cost_price" class="form-control" placeholder="0.00" value="{{ $product->cost_price }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Selling Price:</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs</span>
                                    <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="0.00" value="{{ $product->selling_price }}">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description:</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Product Description">{{ $product->description }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch form-switch-lg mt-2" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ $product->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-3">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
