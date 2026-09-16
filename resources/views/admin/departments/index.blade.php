@extends('layouts.app')

@section('title', 'Manage Departments')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-building me-2"></i>Manage Departments</h2>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Department
        </a>
    </div>
    
    <div class="row">
        @forelse($departments as $department)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                @if($department->image)
                    <img src="{{ Storage::url($department->image) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white text-center py-5">
                        <i class="fas fa-hospital fa-4x"></i>
                    </div>
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">{{ $department->name }}</h5>
                        <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="card-text text-muted">{{ Str::limit($department->description, 100) }}</p>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-user-md me-1"></i> {{ $department->doctors()->count() }} Doctors
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="btn-group w-100">
                        <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline delete-form" style="width: 50%;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100 confirm-delete">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                        <form action="{{ route('admin.departments.toggle-status', $department) }}" method="POST" class="d-inline" style="width: 50%;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-{{ $department->is_active ? 'secondary' : 'success' }} w-100">
                                <i class="fas fa-{{ $department->is_active ? 'ban' : 'check' }} me-1"></i>
                                {{ $department->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            @include('partials.empty-state', [
                'icon' => 'building',
                'title' => 'No Departments Yet',
                'message' => 'Departments organize doctors by specialty. Create your first department to structure the clinic.',
                'actionUrl' => route('admin.departments.create'),
                'actionLabel' => 'Add New Department',
            ])
        </div>
        @endforelse
    </div>
    {{ $departments->links() }}
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.confirm-delete').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('.delete-form');
            
            Swal.fire({
                title: 'Delete Department',
                text: 'Are you sure you want to delete this department? This will also remove all associated doctors.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection