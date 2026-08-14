<x-app-layout>
    <x-slot name="header">
        <div class="row mb-3 pb-1">
            <div class="col-12">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-16 mb-1">Edit Role</h4>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <a href="{{ route('roles.index') }}" class="btn btn-primary"><i class="ri-arrow-left-line align-bottom me-1"></i> Back</a>
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

                    <form method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Name:</label>
                                <input type="text" name="name" class="form-control" placeholder="Role Name" value="{{ $role->name }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fs-15 fw-semibold">Permissions & Access</label>
                                
                                @foreach($groupedPermissions as $groupName => $groupPermissions)
                                <div class="mt-4">
                                    <h5 class="fs-14 fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">{{ $groupName }}</h5>
                                    <div class="row g-4">
                                    @foreach($groupPermissions as $value)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card border border-dashed shadow-none mb-0">
                                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="mb-1 text-capitalize">{{ str_replace('-', ' ', $value->name) }}</h6>
                                                        <p class="text-muted mb-0 fs-12">Allow access to {{ str_replace('-', ' ', $value->name) }}</p>
                                                    </div>
                                                    <label class="toggle-switch mb-0">
                                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}" id="perm_{{ $value->id }}" {{ in_array($value->name, $rolePermissions) ? 'checked' : '' }}>
                                                        <span class="slider"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                                @endforeach

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
    @push('styles')
    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 28px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #e2e5e8;
            transition: .3s;
            border-radius: 34px;
        }
        .toggle-switch .slider:before {
            position: absolute;
            content: "";
            height: 20px; width: 20px;
            left: 4px; bottom: 4px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .toggle-switch input:checked + .slider {
            background-color: #0ab39c;
        }
        .toggle-switch input:checked + .slider:before {
            transform: translateX(32px);
        }
        .toggle-switch .slider:after {
            content: 'OFF';
            color: #878a99;
            display: block;
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 65%;
            font-size: 10px;
            font-weight: 700;
            transition: .3s;
        }
        .toggle-switch input:checked + .slider:after {
            content: 'ON';
            color: #fff;
            left: 35%;
        }
    </style>
    @endpush
</x-app-layout>
