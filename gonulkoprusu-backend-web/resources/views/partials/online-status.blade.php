@php
    $statusUser = $user ?? null;
    $statusClass = $class ?? '';
@endphp

@if($statusUser)
    <span class="online-status {{ $statusUser->isOnline() ? 'online-status--online' : 'online-status--offline' }} {{ $statusClass }}">
        <span class="online-status-dot" aria-hidden="true"></span>
        {{ $statusUser->onlineStatusLabel() }}
    </span>
@endif
