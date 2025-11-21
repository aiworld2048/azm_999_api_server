@extends('layouts.master')

@section('content')
<div class="container">
    <h2 class="mb-4">Agent Profile: <span class="text-primary">{{ $agent->name }}</span></h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Profile Details</strong>
                </div>
                <div class="card-body">
                    @if($agent->profile)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $agent->profile) }}" alt="Profile Picture" class="rounded-circle" width="100" height="100">
                        </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-6"><strong>ID:</strong></div>
                        <div class="col-6">{{ $agent->id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Username:</strong></div>
                        <div class="col-6">{{ $agent->user_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Email:</strong></div>
                        <div class="col-6">{{ $agent->email ?: '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Phone:</strong></div>
                        <div class="col-6">{{ $agent->phone ?: '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Status:</strong></div>
                        <div class="col-6">
                            @if($agent->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Created At:</strong></div>
                        <div class="col-6">{{ $agent->created_at }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Updated At:</strong></div>
                        <div class="col-6">{{ $agent->updated_at }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Last Login:</strong></div>
                        <div class="col-6">{{ $agent->last_login_at ?? '-' }}</div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Agent:</strong></div>
                        <div class="col-6">{{ $agent->agent->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Agent ID:</strong></div>
                        <div class="col-6">{{ $agent->agent_id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Agent Phone:</strong></div>
                        <div class="col-6">{{ $agent->agent->phone ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Agent Balance:</strong></div>
                        <div class="col-6">
                            <span class="badge bg-info text-dark">
                                {{ $agent->agent ? number_format((float) $agent->agent->balance, 2) : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Role:</strong></div>
                        <div class="col-6">
                            @foreach($agent->roles as $role)
                                <span class="badge bg-secondary">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Permissions:</strong></div>
                        <div class="col-6">
                            @foreach ($agent->permissions as $permission)
                                <span class="badge bg-light text-dark border mb-1">{{ $permission->title }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4">
                        @can('agent_edit')
                            <a href="{{ route('admin.agent.edit', $agent->id) }}" class="btn btn-warning me-2">Edit Profile</a>
                        @endcan
                       
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection