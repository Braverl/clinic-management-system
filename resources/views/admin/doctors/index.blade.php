@extends('layouts.app')

@section('title', 'Manage Doctors')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-md me-2"></i>Manage Doctors</h2>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Doctor
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="doctors-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Specialization</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->id }}</td>
                            <td>
                                @if($doctor->user->profile_image)
                                    <img src="{{ Storage::url($doctor->user->profile_image) }}" width="40" height="40" class="rounded-circle">
                                @else
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>Dr. {{ $doctor->user->name }}</td>
                            <td>{{ $doctor->user->email }}</td>
                            <td>{{ $doctor->department->name ?? 'N/A' }}</td>
                            <td>{{ $doctor->specialization }}</td>
                            <td>${{ number_format($doctor->consultation_fee, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $doctor->user->is_active ? 'success' : 'danger' }}">
                                    {{ $doctor->user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger confirm-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.doctors.toggle-status', $doctor) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $doctor->user->is_active ? 'secondary' : 'success' }}" title="{{ $doctor->user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $doctor->user->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No doctors found. <a href="{{ route('admin.doctors.create') }}">Add your first doctor</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $doctors->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.confirm-delete').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('.delete-form');
            
            Swal.fire({
                title: 'Delete Doctor',
                text: 'Are you sure you want to delete this doctor? This action cannot be undone.',
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