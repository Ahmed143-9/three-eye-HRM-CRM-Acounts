@extends('layouts.admin')

@section('page-title')
    {{ __('Pending User Approvals') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('Pending Approvals') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="ti ti-user-check me-2 text-warning"></i>
                    {{ __('Users Awaiting Super Admin Approval') }}
                    @if($pendingUsers->count() > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingUsers->count() }}</span>
                    @endif
                </h5>
            </div>

            <div class="card-body p-0">
                @if($pendingUsers->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-circle-check" style="font-size: 3rem; color: #28a745;"></i>
                        <h5 class="mt-3 text-muted">{{ __('No pending approvals. All users are up to date.') }}</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <img src="{{ !empty($user->avatar) ? \App\Models\Utility::get_file('uploads/avatar/') . $user->avatar : asset(\Storage::url('uploads/avatar/avatar.png')) }}"
                                                    alt="{{ $user->name }}" class="rounded-circle" width="36" height="36">
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ ucfirst($user->type) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $user->creator->name ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted text-sm">{{ $user->created_at->format('d M Y, H:i') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            {{-- Approve --}}
                                            <form action="{{ route('users.approve', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('{{ __('Approve this user and grant them login access?') }}')"
                                                    data-bs-toggle="tooltip" title="{{ __('Approve & Activate') }}">
                                                    <i class="ti ti-check me-1"></i>{{ __('Approve') }}
                                                </button>
                                            </form>

                                            {{-- Reject & Delete --}}
                                            <form action="{{ route('users.reject', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('{{ __('Reject and permanently delete this user account?') }}')"
                                                    data-bs-toggle="tooltip" title="{{ __('Reject & Delete') }}">
                                                    <i class="ti ti-x me-1"></i>{{ __('Reject') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
