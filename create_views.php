<?php
$entities = [
    'clients' => ['single' => 'Client', 'route' => 'accounting-clients'],
    'suppliers' => ['single' => 'Supplier', 'route' => 'suppliers'],
    'consultants' => ['single' => 'Consultant', 'route' => 'consultants'],
];

$baseDir = __DIR__ . '/resources/views/accounting_setup/';
if(!is_dir($baseDir)) mkdir($baseDir, 0777, true);

foreach($entities as $folder => $info) {
    if(!is_dir($baseDir.$folder)) mkdir($baseDir.$folder, 0777, true);
    
    $single = $info['single'];
    $route = $info['route'];
    
    $index = <<<EOT
@extends('layouts.admin')
@section('page-title', __('Manage {$single}s'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('{$single}s') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="{{ route('{$route}.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Unique ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\${$folder} as \$item)
                                <tr>
                                    <td>{{ \$item->unique_id }}</td>
                                    <td>{{ \$item->name }}</td>
                                    <td>{{ \$item->email }}</td>
                                    <td>{{ \$item->phone }}</td>
                                    <td>
                                        @if(\$item->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('{$route}.edit', \$item->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                            <form method="POST" action="{{ route('{$route}.destroy', \$item->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                </button>
                                            </form>
                                        </div>
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
@endsection
EOT;

    $create = <<<EOT
@extends('layouts.admin')
@section('page-title', __('Create {$single}'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('{$route}.index') }}">{{ __('{$single}s') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create') }}</li>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('{$route}.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Unique ID') }}</label>
                            <input type="text" class="form-control" name="unique_id" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Billing Address') }}</label>
                            <textarea class="form-control" name="billing_address" rows="3"></textarea>
                        </div>
                        @if("{$single}" == "Client")
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Delivery Address') }}</label>
                            <textarea class="form-control" name="delivery_address" rows="3"></textarea>
                        </div>
                        @endif
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Bank Details') }}</label>
                            <textarea class="form-control" name="bank_details" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('File Attachment') }}</label>
                            <input type="file" class="form-control" name="file_attachment">
                        </div>
                        <div class="form-group col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
EOT;

    $edit = <<<EOT
@extends('layouts.admin')
@section('page-title', __('Edit {$single}'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('{$route}.index') }}">{{ __('{$single}s') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
<div class="row">
        @include('layouts.account_setup')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('{$route}.update', \${strtolower($single)}->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Unique ID') }}</label>
                            <input type="text" class="form-control" name="unique_id" value="{{ \${strtolower($single)}->unique_id }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ \${strtolower($single)}->name }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="email" value="{{ \${strtolower($single)}->email }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control" name="phone" value="{{ \${strtolower($single)}->phone }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Billing Address') }}</label>
                            <textarea class="form-control" name="billing_address" rows="3">{{ \${strtolower($single)}->billing_address }}</textarea>
                        </div>
                        @if("{$single}" == "Client")
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Delivery Address') }}</label>
                            <textarea class="form-control" name="delivery_address" rows="3">{{ \${strtolower($single)}->delivery_address }}</textarea>
                        </div>
                        @endif
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('Bank Details') }}</label>
                            <textarea class="form-control" name="bank_details" rows="3">{{ \${strtolower($single)}->bank_details }}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ __('File Attachment') }}</label>
                            <input type="file" class="form-control" name="file_attachment">
                            @if(\${strtolower($single)}->file_attachment)
                                <a href="{{ Storage::url(\${strtolower($single)}->file_attachment) }}" target="_blank" class="text-primary mt-2 d-block">View Current Attachment</a>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" {{ \${strtolower($single)}->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
EOT;

    file_put_contents($baseDir.$folder.'/index.blade.php', $index);
    file_put_contents($baseDir.$folder.'/create.blade.php', $create);
    file_put_contents($baseDir.$folder.'/edit.blade.php', $edit);
}
echo "done";
