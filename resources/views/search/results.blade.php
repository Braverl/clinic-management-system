@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-search me-2"></i>Search</h2>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('search.index') }}" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="query" value="{{ $query ?? '' }}" class="form-control" placeholder="Search patients, doctors, appointments..." autofocus>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(empty($query))
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search fa-4x mb-3 opacity-25"></i>
            <h5 class="text-muted">Search the system</h5>
            <p>Enter a name, email, or appointment number above to search.</p>
        </div>
    @elseif(empty($results))
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search-minus fa-4x mb-3 opacity-25"></i>
            <h5 class="text-muted">No results for "{{ $query }}"</h5>
            <p>Try a different keyword.</p>
        </div>
    @else
        <p class="text-muted">Results for <strong class="text-primary">"{{ $query }}"</strong></p>

        @if(!empty($results['patients']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="fas fa-users me-2"></i>Patients
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['patients'] as $r)
                            <tr>
                                <td>{{ $r['name'] }}</td>
                                <td>{{ $r['email'] ?? '' }}</td>
                                <td>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.patients.show', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                                    @elseif(auth()->user()->isDoctor())
                                    <a href="{{ route('doctor.patients.history', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($results['doctors']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="fas fa-user-md me-2"></i>Doctors
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['doctors'] as $r)
                            <tr>
                                <td>{{ $r['name'] }}</td>
                                <td>{{ $r['specialization'] ?? '' }}</td>
                                <td>{{ $r['department'] ?? '' }}</td>
                                <td>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.doctors.edit', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit</a>
                                    @else
                                    <a href="{{ route('patient.appointments.book') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-calendar-plus me-1"></i>Book</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($results['appointments']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-check me-2"></i>Appointments
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['appointments'] as $r)
                            <tr>
                                <td>{{ $r['number'] }}</td>
                                <td>{{ $r['patient'] ?? '' }}</td>
                                <td>{{ $r['doctor'] ?? '' }}</td>
                                <td>{{ $r['date'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $r['status'] == 'pending' ? 'warning' : ($r['status'] == 'confirmed' ? 'info' : ($r['status'] == 'completed' ? 'success' : 'danger')) }}">
                                        {{ ucfirst($r['status']) }}
                                    </span>
                                </td>
                                <td>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.appointments.show', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                                    @elseif(auth()->user()->isDoctor())
                                    <a href="{{ route('doctor.appointments.show', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                                    @else
                                    <a href="{{ route('patient.appointments.show', $r['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i>View</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection