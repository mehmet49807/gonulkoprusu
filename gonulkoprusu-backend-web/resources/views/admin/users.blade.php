@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<h1 class="admin-page-title">Kullanıcı Yönetimi</h1>

<div class="admin-table-wrap admin-table-wrap--dropdown"><table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Kullanıcı Adı</th>
            <th>Ad Soyad</th>
            <th>E-posta</th>
            <th>Cinsiyet</th>
            <th>Durum</th>
            <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->gender === 'male' ? 'Erkek' : 'Kadın' }}</td>
            <td>
                @if($user->is_banned)
                    <span class="badge badge-banned">Banlı</span>
                @else
                    <span class="badge badge-resolved">Aktif</span>
                @endif
            </td>
            <td>
                <div class="admin-action-dropdown" data-dropdown>
                    <button type="button" class="admin-action-dropdown-toggle btn btn-outline" aria-expanded="false" aria-haspopup="true">
                        İşlemler
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="admin-action-dropdown-menu" role="menu">
                        <button
                            type="button"
                            class="admin-action-item admin-edit-user-btn"
                            role="menuitem"
                            data-user-id="{{ $user->id }}"
                            data-first-name="{{ $user->first_name }}"
                            data-last-name="{{ $user->last_name }}"
                            data-email="{{ $user->email }}"
                            data-phone="{{ $user->phone }}"
                            data-country="{{ $user->country ?? 'Türkiye' }}"
                            data-city="{{ $user->city }}"
                            data-district="{{ $user->district }}"
                            data-is-banned="{{ $user->is_banned ? '1' : '0' }}"
                            data-banned-reason="{{ $user->banned_reason ?? '' }}"
                            data-username="{{ $user->username }}"
                        >Düzenle</button>

                        @if(!$user->is_banned)
                        <button
                            type="button"
                            class="admin-action-item admin-edit-user-btn admin-action-item--warn"
                            role="menuitem"
                            data-user-id="{{ $user->id }}"
                            data-first-name="{{ $user->first_name }}"
                            data-last-name="{{ $user->last_name }}"
                            data-email="{{ $user->email }}"
                            data-phone="{{ $user->phone }}"
                            data-country="{{ $user->country ?? 'Türkiye' }}"
                            data-city="{{ $user->city }}"
                            data-district="{{ $user->district }}"
                            data-is-banned="1"
                            data-banned-reason=""
                            data-username="{{ $user->username }}"
                        >Banla</button>
                        @else
                        <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="admin-action-form" role="none">
                            @csrf
                            <button type="submit" class="admin-action-item admin-action-item--success" role="menuitem">Banı Kaldır</button>
                        </form>
                        @endif

                        <a href="{{ rtrim(config('app.url'), '/') }}/users/{{ $user->username }}" class="admin-action-item" role="menuitem" target="_blank" rel="noopener">Profili Gör</a>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table></div>

{{ $users->links() }}

<dialog id="adminUserEditDialog" class="admin-dialog">
    <form method="POST" id="adminUserEditForm" class="admin-dialog-form">
        @csrf
        @method('PUT')
        <header class="admin-dialog-header">
            <h2 id="adminUserEditTitle">Kullanıcı Düzenle</h2>
            <button type="button" class="admin-dialog-close" data-close-dialog aria-label="Kapat">×</button>
        </header>

        <div class="form-group">
            <label>Ad</label>
            <input type="text" name="first_name" id="edit_first_name" required>
        </div>
        <div class="form-group">
            <label>Soyad</label>
            <input type="text" name="last_name" id="edit_last_name" required>
        </div>
        <div class="form-group">
            <label>E-posta</label>
            <input type="email" name="email" id="edit_email" required>
        </div>
        <div class="form-group">
            <label>Telefon</label>
            <input type="text" name="phone" id="edit_phone">
        </div>
        <div class="form-group">
            <label>Ülke</label>
            <input type="text" name="country" id="edit_country">
        </div>
        <div class="form-group">
            <label>Şehir</label>
            <input type="text" name="city" id="edit_city" required>
        </div>
        <div class="form-group">
            <label>İlçe</label>
            <input type="text" name="district" id="edit_district">
        </div>
        <div class="form-group admin-checkbox-group">
            <label>
                <input type="checkbox" name="is_banned" id="edit_is_banned" value="1">
                Kullanıcıyı banla
            </label>
        </div>
        <div class="form-group" id="edit_banned_reason_wrap">
            <label>Ban sebebi</label>
            <textarea name="banned_reason" id="edit_banned_reason" rows="3" style="width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);background:var(--cream)"></textarea>
        </div>

        <footer class="admin-dialog-footer">
            <button type="button" class="btn btn-outline" data-close-dialog>İptal</button>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </footer>
    </form>
</dialog>

<script>
(function () {
    const dialog = document.getElementById('adminUserEditDialog');
    const form = document.getElementById('adminUserEditForm');
    const title = document.getElementById('adminUserEditTitle');
    const bannedWrap = document.getElementById('edit_banned_reason_wrap');
    const bannedCheck = document.getElementById('edit_is_banned');
    const updateUrlTemplate = @json(route('admin.users.update', ['user' => '__ID__']));

    function closeAllDropdowns() {
        document.querySelectorAll('[data-dropdown]').forEach(function (wrap) {
            wrap.classList.remove('is-open');
            const toggle = wrap.querySelector('.admin-action-dropdown-toggle');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
    }

    function openDropdown(wrap) {
        closeAllDropdowns();
        wrap.classList.add('is-open');
        const toggle = wrap.querySelector('.admin-action-dropdown-toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
    }

    document.querySelectorAll('[data-dropdown]').forEach(function (wrap) {
        const toggle = wrap.querySelector('.admin-action-dropdown-toggle');

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (wrap.classList.contains('is-open')) {
                closeAllDropdowns();
            } else {
                openDropdown(wrap);
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-dropdown]')) {
            return;
        }
        closeAllDropdowns();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
            if (dialog.open) {
                dialog.close();
            }
        }
    });

    function toggleBannedReason() {
        bannedWrap.hidden = !bannedCheck.checked;
    }

    bannedCheck.addEventListener('change', toggleBannedReason);

    function closeDialog() {
        if (dialog.open) {
            dialog.close();
        }
    }

    function openEditModal(btn) {
        closeAllDropdowns();
        const id = btn.dataset.userId;
        form.action = updateUrlTemplate.replace('__ID__', id);
        title.textContent = btn.dataset.username + ' — Düzenle';
        document.getElementById('edit_first_name').value = btn.dataset.firstName || '';
        document.getElementById('edit_last_name').value = btn.dataset.lastName || '';
        document.getElementById('edit_email').value = btn.dataset.email || '';
        document.getElementById('edit_phone').value = btn.dataset.phone || '';
        document.getElementById('edit_country').value = btn.dataset.country || '';
        document.getElementById('edit_city').value = btn.dataset.city || '';
        document.getElementById('edit_district').value = btn.dataset.district || '';
        bannedCheck.checked = btn.dataset.isBanned === '1';
        document.getElementById('edit_banned_reason').value = btn.dataset.bannedReason || '';
        toggleBannedReason();
        dialog.showModal();
    }

    document.querySelectorAll('.admin-edit-user-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openEditModal(btn);
        });
    });

    document.querySelectorAll('[data-close-dialog]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            closeDialog();
        });
    });

    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) {
            closeDialog();
        }
    });
})();
</script>
@endsection
