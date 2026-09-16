@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="chat-app-wrapper">
    <div class="chat-app" id="chatApp">
        <!-- ============ SIDEBAR (conversation list) ============ -->
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Chats</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="newChatBtn" title="New chat">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="input-group mt-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="chatSearch" placeholder="Search conversations...">
                </div>
            </div>
            <div class="chat-conversation-list" id="conversationList">
                @forelse($conversations as $conversation)
                    @php
                        $other = $conversation->getOtherUser(auth()->id());
                        $unread = $conversation->getUnreadCount(auth()->id());
                    @endphp
                    <a href="#" class="chat-conversation-item {{ $loop->first ? 'active' : '' }}" data-conversation-id="{{ $conversation->id }}">
                        <div class="chat-avatar">
                            @if($other->profile_image)
                                <img src="{{ Storage::url($other->profile_image) }}" alt="{{ $other->name }}">
                            @else
                                <div class="chat-avatar-placeholder">
                                    <i class="fas fa-{{ $other->isDoctor() ? 'user-md' : ($other->isAdmin() ? 'user-shield' : 'user') }}"></i>
                                </div>
                            @endif
                        </div>
                        <div class="chat-conversation-info">
                            <div class="chat-conversation-name">{{ $other->name }}</div>
                            <div class="chat-conversation-preview">
                                @if($conversation->last_message_preview)
                                    {{ Str::limit($conversation->last_message_preview, 30) }}
                                @else
                                    <span class="text-muted">No messages yet</span>
                                @endif
                            </div>
                        </div>
                        <div class="chat-conversation-meta">
                            @if($conversation->last_message_at)
                                <small class="text-muted">{{ $conversation->last_message_at->diffForHumans() }}</small>
                            @endif
                            @if($unread > 0)
                                <span class="chat-unread-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                        <p>No conversations yet.<br>Click <i class="fas fa-plus"></i> to start a new chat.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <!-- ============ MAIN (message thread) ============ -->
        <section class="chat-main">
            <!-- Chat header -->
            <div class="chat-main-header" id="chatHeader">
                <div class="chat-avatar">
                    <div class="chat-avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="chat-main-header-info">
                    <h6 class="mb-0">Select a conversation</h6>
                    <small class="text-muted">Choose a chat to start messaging</small>
                </div>
                <button type="button" class="btn btn-sm btn-light ms-auto" id="deleteChatBtn" style="display:none;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <!-- Messages area -->
            <div class="chat-messages" id="chatMessages">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comment-slash fa-3x mb-3 opacity-50"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            </div>

            <!-- New chat: user search -->
            <div class="chat-user-search" id="chatUserSearch" style="display:none;">
                <div class="input-group p-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="userSearchInput" placeholder="Search doctors, patients, admins by name or email...">
                </div>
                <div id="userSearchResults" class="chat-user-results"></div>
            </div>

            <!-- Message composer -->
            <div class="chat-composer" id="chatComposer" style="display:none;">
                <form id="chatSendForm" autocomplete="off">
                    <input type="hidden" id="chatRecipientId">
                    <div class="chat-composer-row">
                        <button type="button" class="btn btn-outline-secondary btn-sm chat-emoji-btn" title="Emoji"><i class="fas fa-smile"></i></button>
                        <textarea id="chatMessageInput" class="form-control" rows="1" placeholder="Type a message..." style="max-height: 120px; resize: none;"></textarea>
                        <button type="submit" class="btn btn-primary chat-send-btn" title="Send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@push('scripts')
<script>
    let activeConversationId = null;
    let activeRecipientId = null;
    let isFirstLoad = true;
    let pollTimer = null;

    const currentUserId = @json(auth()->id());
    const storageBase = '{{ Storage::url('') }}';

    $(document).ready(function() {
        // Mark initial conversation active
        const first = $('.chat-conversation-item.active');
        if (first.length) {
            loadConversation(first.data('conversation-id'));
        }

        // New chat button - show user search
        $('#newChatBtn').on('click', function() {
            $('#chatUserSearch').slideToggle();
            $('#userSearchInput').val('').focus();
            $('#userSearchResults').empty();
        });

        // User search with debounce
        let searchTimeout;
        $('#userSearchInput').on('keyup', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            if (query.length < 1) {
                $('#userSearchResults').empty();
                return;
            }
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route("chat.search-users") }}',
                    method: 'GET',
                    data: { query: query },
                    success: function(response) {
                        let html = '';
                        response.users.forEach(function(user) {
                            html += `
                                <a href="#" class="chat-user-result" data-user-id="${user.id}" data-name="${user.name}">
                                    <div class="chat-avatar">
                                        ${user.profile_image
                                            ? `<img src="${storageBase}${user.profile_image}">`
                                            : `<div class="chat-avatar-placeholder"><i class="fas fa-user"></i></div>`}
                                    </div>
                                    <div>
                                        <strong>${user.name}</strong>
                                        <small class="d-block text-muted">${user.email} &middot; ${user.role}</small>
                                    </div>
                                </a>`;
                        });
                        if (response.users.length === 0) {
                            html = '<div class="text-center text-muted py-3">No matching users found</div>';
                        }
                        $('#userSearchResults').html(html);
                    }
                });
            }, 300);
        });

        // Click a user result -> open conversation
        $(document).on('click', '.chat-user-result', function(e) {
            e.preventDefault();
            const userId = $(this).data('user-id');
            const name = $(this).data('name');
            // Conversations are auto-created on first message; send navigates here
            openConversationWithUser(userId, name);
            $('#chatUserSearch').slideUp();
        });

        // Select conversation from list
        $(document).on('click', '.chat-conversation-item', function(e) {
            e.preventDefault();
            $('.chat-conversation-item').removeClass('active');
            $(this).addClass('active');
            loadConversation($(this).data('conversation-id'));
        });

        // Live search in conversation list
        $('#chatSearch').on('keyup', function() {
            const q = $(this).val().toLowerCase();
            $('.chat-conversation-item').each(function() {
                const name = $(this).find('.chat-conversation-name').text().toLowerCase();
                $(this).toggle(name.indexOf(q) !== -1);
            });
        });

        // Send message
        $('#chatSendForm').on('submit', function(e) {
            e.preventDefault();
            const body = $('#chatMessageInput').val().trim();
            if (!body || !activeRecipientId) return;

            $.ajax({
                url: '{{ route("chat.send") }}',
                method: 'POST',
                data: {
                    recipient_id: activeRecipientId,
                    body: body
                },
                success: function(response) {
                    $('#chatMessageInput').val('');
                    appendMessage(response.message, true);
                    refreshConversationList();
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to send message', 'error');
                }
            });
        });

        // Enter key sends (Shift+Enter = newline)
        $('#chatMessageInput').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#chatSendForm').submit();
            }
        });

        // Emoji insert
        $('.chat-emoji-btn').on('click', function() {
            const input = $('#chatMessageInput');
            const emojis = ['😀','😁','😂','🤔','😅','😊','👍','👏','🙏','💙','🔥','🎉','❤️','😷','💊','🩺'];
            const emoji = emojis[Math.floor(Math.random() * emojis.length)];
            input.val(input.val() + emoji);
            input.focus();
        });

        // Poll for new messages every 5 seconds
        pollTimer = setInterval(function() {
            if (activeConversationId) {
                refreshConversationList();
                // Also refresh messages incrementally
                $.ajax({
                    url: `/chat/conversations/${activeConversationId}/messages`,
                    method: 'GET',
                    success: function(response) {
                        renderMessages(response.messages);
                        refreshUnreadBadge();
                    }
                });
            }
        }, 5000);

        // Initial unread badge update
        refreshUnreadBadge();
    });

    function loadConversation(conversationId) {
        activeConversationId = conversationId;
        $('#chatComposer').show();

        $.ajax({
            url: `/chat/conversations/${conversationId}/messages`,
            method: 'GET',
            success: function(response) {
                const conv = response.conversation;
                const otherUser = conv.user_one.id === currentUserId ? conv.user_two : conv.user_one;
                activeRecipientId = otherUser.id;

                // Update header
                const avatarHtml = otherUser.profile_image
                    ? `<img src="${storageBase}${otherUser.profile_image}">`
                    : `<div class="chat-avatar-placeholder"><i class="fas fa-user"></i></div>`;
                $('#chatHeader').find('.chat-avatar').html(avatarHtml);
                $('#chatHeader .chat-main-header-info h6').text(otherUser.name);
                $('#chatHeader .chat-main-header-info small').text(capitalize(otherUser.role));
                $('#deleteChatBtn').show();

                renderMessages(response.messages);
                refreshConversationList();
                refreshUnreadBadge();
            }
        });
    }

    function openConversationWithUser(userId, name) {
        // We need the conversation. If none exists, sending a message will create it.
        activeRecipientId = userId;
        activeConversationId = null;
        $('#chatComposer').show();

        const avatarHtml = `<div class="chat-avatar-placeholder"><i class="fas fa-user"></i></div>`;
        $('#chatHeader').find('.chat-avatar').html(avatarHtml);
        $('#chatHeader .chat-main-header-info h6').text(name);
        $('#chatHeader .chat-main-header-info small').text('New conversation');
        $('#deleteChatBtn').hide();
        $('#chatMessages').html('<div class="text-center text-muted py-5"><p>Start the conversation by sending a message!</p></div>');
    }

    function renderMessages(messages) {
        let html = '';
        messages.forEach(function(m) {
            const mine = m.sender_id === currentUserId;
            const isUnsent = m.status === 'unsent';
            html += appendMessageHTML(m, mine, isUnsent);
        });
        $('#chatMessages').html(html);
        scrollMessagesToBottom();
        setupMessageActions();
    }

    function appendMessage(message, mine) {
        const isUnsent = message.status === 'unsent';
        $('#chatMessages').append(appendMessageHTML(message, mine, isUnsent));
        scrollMessagesToBottom();
        setupMessageActions();
    }

    function appendMessageHTML(m, mine, isUnsent) {
        let body = '';

        // Visibility rules: 
        // - unsent -> "This message was unsent"
        // - deleted_for_sender + I'm sender, or deleted_for_recipient + I'm recipient -> deleted
        if (isUnsent) {
            body = '<em class="chat-unsent-text"><i class="fas fa-ban me-1"></i>This message was unsent</em>';
        } else if ((m.deleted_for_sender && mine) || (m.deleted_for_recipient && !mine)) {
            body = '<em class="text-muted"><i class="fas fa-trash me-1"></i>This message was deleted</em>';
        } else {
            body = escapeHtml(m.body || '');
            body = body.replace(/\n/g, '<br>');
        }

        const edited = m.is_edited ? ' <small class="chat-edited">(edited)</small>' : '';
        const time = m.sender && m.sender.id !== currentUserId && !mine
            ? m.sender.name.replace(/^Dr\.?\s*/i, '') + ' &middot; ' + m.created_at
            : m.created_at;

        const timeDisplay = formatTime(m.created_at);
        const sentIcon = mine
            ? (m.read_at ? '<i class="fas fa-check-double chat-read-icon" title="Read"></i>' : '<i class="fas fa-check chat-sent-icon" title="Sent"></i>')
            : '';

        const actions = mine && !isUnsent ? `
            <div class="chat-msg-actions">
                <button class="btn btn-sm btn-light chat-edit-btn" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-warning chat-unsend-btn" title="Unsend for everyone"><i class="fas fa-ban"></i></button>
                <div class="btn-group">
                    <button class="btn btn-sm btn-light dropdown-toggle no-arrow" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="#" class="dropdown-item chat-del-me" data-scope="sender"><i class="fas fa-trash me-2"></i>Delete for me</a></li>
                        <li class="dropdown-item chat-del-both d-none" data-scope="recipient"><i class="fas fa-user-slash me-2"></i>Delete for both</a></li>
                    </ul>
                </div>
            </div>` : (isUnsent ? '' : `
            <div class="chat-msg-actions">
                <div class="btn-group">
                    <button class="btn btn-sm btn-light dropdown-toggle no-arrow" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="#" class="dropdown-item chat-del-me" data-scope="sender"><i class="fas fa-trash me-2"></i>Delete for me</a></li>
                    </ul>
                </div>
            </div>`);

        return `
            <div class="chat-message ${mine ? 'mine' : 'theirs'}" data-id="${m.id}">
                <div class="chat-bubble">
                    <div class="chat-bubble-text">${body}${edited}</div>
                    <div class="chat-msg-meta">
                        <small class="text-muted">${timeDisplay}</small> ${sentIcon}
                    </div>
                </div>
                ${actions}
            </div>`;
    }

    function setupMessageActions() {
        // Edit
        $('.chat-edit-btn').off('click').on('click', function() {
            const $msg = $(this).closest('.chat-message');
            const id = $msg.data('id');
            const $bubble = $msg.find('.chat-bubble-text');
            const original = $bubble.clone().children().remove().end().text();

            $bubble.html(`
                <div class="chat-edit-form">
                    <textarea class="form-control form-control-sm chat-edit-input" rows="2">${escapeHtml(original)}</textarea>
                    <div class="btn-group btn-group-sm mt-1 w-100">
                        <button type="button" class="btn btn-primary chat-edit-save">Save</button>
                        <button type="button" class="btn btn-secondary chat-edit-cancel">Cancel</button>
                    </div>
                </div>`);

            $msg.find('.chat-edit-save').on('click', function() {
                const newBody = $(this).closest('.chat-edit-form').find('.chat-edit-input').val().trim();
                if (!newBody) return;
                $.ajax({
                    url: '{{ route("chat.edit") }}',
                    method: 'POST',
                    data: { message_id: id, body: newBody },
                    success: function(response) {
                        showToast('Message updated', 'success');
                        loadConversation(activeConversationId);
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Failed to edit', 'error');
                    }
                });
            });
            $msg.find('.chat-edit-cancel').on('click', function() {
                loadConversation(activeConversationId);
            });
        });

        // Unsend
        $('.chat-unsend-btn').off('click').on('click', function() {
            const id = $(this).closest('.chat-message').data('id');
            Swal.fire({
                title: 'Unsend message?',
                text: 'This will remove the message for everyone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Unsend',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/chat/unsend/${id}`,
                        method: 'POST',
                        success: function(response) {
                            showToast('Message unsent for everyone', 'success');
                            loadConversation(activeConversationId);
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON?.message || 'Failed', 'error');
                        }
                    });
                }
            });
        });

        // Delete for me
        $('.chat-del-me').off('click').on('click', function(e) {
            e.preventDefault();
            const id = $(this).closest('.chat-message').data('id');
            deleteMessage(id, 'sender');
        });

        // Delete for both (recipient's copy)
        $('.chat-del-both').off('click').on('click', function(e) {
            e.preventDefault();
            const id = $(this).closest('.chat-message').data('id');
            deleteMessage(id, 'recipient');
        });
    }

    function deleteMessage(id, scope) {
        Swal.fire({
            title: 'Delete message?',
            text: scope === 'sender' ? 'Delete this message for you?' : 'Delete this message for both?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("chat.delete") }}',
                    method: 'POST',
                    data: { message_id: id, scope: scope },
                    success: function(response) {
                        showToast('Message deleted', 'success');
                        loadConversation(activeConversationId);
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Failed', 'error');
                    }
                });
            }
        });
    }

    function refreshConversationList() {
        $.ajax({
            url: '{{ route("chat.conversations") }}',
            method: 'GET',
            success: function(response) {
                let html = '';
                response.conversations.forEach(function(c) {
                    const avatar = c.other_user.profile_image
                        ? `<img src="${storageBase}${c.other_user.profile_image}">`
                        : `<div class="chat-avatar-placeholder"><i class="fas fa-user"></i></div>`;
                    const unread = c.unread_count > 0 ? `<span class="chat-unread-badge">${c.unread_count > 9 ? '9+' : c.unread_count}</span>` : '';
                    html += `
                        <a href="#" class="chat-conversation-item ${c.id === activeConversationId ? 'active' : ''}" data-conversation-id="${c.id}">
                            <div class="chat-avatar">${avatar}</div>
                            <div class="chat-conversation-info">
                                <div class="chat-conversation-name">${escapeHtml(c.other_user.name)}</div>
                                <div class="chat-conversation-preview">${c.last_message ? escapeHtml(c.last_message) : 'No messages yet'}</div>
                            </div>
                            <div class="chat-conversation-meta">
                                ${c.last_message_at ? `<small class="text-muted">${timeAgoFr(c.last_message_at)}</small>` : ''}
                                ${unread}
                            </div>
                        </a>`;
                });
                if (response.conversations.length === 0) {
                    html = '<div class="text-center py-5 text-muted"><i class="fas fa-comments fa-3x mb-3 opacity-50"></i><p>No conversations yet.</p></div>';
                }
                $('#conversationList').html(html);
            }
        });
    }

    function refreshUnreadBadge() {
        $.ajax({
            url: '{{ route("chat.unread-count") }}',
            method: 'GET',
            success: function(response) {
                const $badge = $('#chatUnreadBadge');
                if (response.count > 0) {
                    $badge.text(response.count > 9 ? '9+' : response.count).show();
                } else {
                    $badge.hide();
                }
            }
        });
    }

    function scrollMessagesToBottom() {
        const $m = $('#chatMessages');
        $m.scrollTop($m.prop('scrollHeight'));
    }

    function formatTime(dateStr) {
        try {
            const d = new Date(dateStr.replace(' ', 'T'));
            const now = new Date();
            const sameDay = d.toDateString() === now.toDateString();
            if (sameDay) {
                return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
            return d.toLocaleDateString([], { month: 'short', day: 'numeric' }) + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return '';
        }
    }

    function timeAgoFr(dateStr) {
        try {
            const d = new Date(dateStr);
            const s = Math.floor((Date.now() - d.getTime()) / 1000);
            if (s < 60) return 'now';
            if (s < 3600) return Math.floor(s / 60) + 'm';
            if (s < 86400) return Math.floor(s / 3600) + 'h';
            if (s < 86400 * 7) return Math.floor(s / 86400) + 'd';
            return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
        } catch (e) {
            return '';
        }
    }

    function capitalize(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endpush
@endsection