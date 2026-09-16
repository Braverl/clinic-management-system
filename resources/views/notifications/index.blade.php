@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell me-2"></i>Notifications
            @if($unreadCount > 0)
            <span class="badge bg-danger ms-2">{{ $unreadCount }} unread</span>
            @endif
        </h2>
        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-check-double me-2"></i>Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($notifications->count())
                @foreach($notifications as $notification)
                <div class="d-flex align-items-start p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $notification->is_read ? 'bg-secondary bg-opacity-10' : 'bg-primary text-white' }}" style="width: 44px; height: 44px; background-color: {{ $notification->is_read ? '#e2e8f0' : 'var(--brand-500)' }};">
                            <i class="fas fa-{{ $notification->type === 'success' ? 'check-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : ($notification->type === 'error' ? 'times-circle' : 'info-circle')) }} {{ $notification->is_read ? 'text-secondary' : 'text-white' }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 {{ $notification->is_read ? '' : 'fw-bold' }}">{{ $notification->title }}</h6>
                                <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-end ms-3">
                                @if(!$notification->is_read)
                                <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if($notification->link)
                                <a href="{{ $notification->link }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                {{ $notifications->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5>No Notifications</h5>
                    <p class="text-muted">You're all caught up! New notifications will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection