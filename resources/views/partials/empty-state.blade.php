<div class="empty-state py-5 text-center">
    <div class="empty-state-icon">
        <i class="fas fa-{{ $icon ?? 'inbox' }}"></i>
    </div>
    <h5 class="empty-state-title mt-3 mb-1">{{ $title }}</h5>
    <p class="empty-state-message text-muted mb-4">{{ $message }}</p>
    @if(isset($actionUrl) && isset($actionLabel))
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ $actionLabel }}
        </a>
    @endif
</div>

<style>
.empty-state-icon {
    width: 84px;
    height: 84px;
    margin: 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #6366f1;
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.empty-state-title { font-family: 'Poppins', sans-serif; font-weight: 600; color: #0f172a; }
.empty-state-message { max-width: 380px; margin-left: auto; margin-right: auto; }
</style>