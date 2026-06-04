@extends('layouts.app')

@section('title', 'Manage Patients')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users me-2"></i>Manage Patients</h2>
        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Patient
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control live-search" placeholder="Search patients..." data-target="#patients-table">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="patients-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Blood Group</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>{{ $patient->id }}</td>
                            <td class="text-center">
                                @if($patient->user->profile_image)
                                    <img src="{{ Storage::url($patient->user->profile_image) }}" width="40" height="40" class="rounded-circle object-fit-cover" style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $patient->user->name }}</td>
                            <td>{{ $patient->user->email }}</td>
                            <td>{{ $patient->user->phone }}</td>
                            <td><span class="badge bg-info">{{ $patient->blood_group ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge bg-{{ $patient->user->is_active ? 'success' : 'danger' }}">
                                    {{ $patient->user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger confirm-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.patients.toggle-status', $patient) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $patient->user->is_active ? 'secondary' : 'success' }}">
                                            <i class="fas fa-{{ $patient->user->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection