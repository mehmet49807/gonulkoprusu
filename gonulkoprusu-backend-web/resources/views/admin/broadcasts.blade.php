@extends('layouts.admin')

@section('title', 'Duyuru Sistemi')

@section('content')
<h1 class="admin-page-title">Admin Duyuru Sistemi</h1>

<div class="form-card" style="margin-bottom:32px">
    <h3 style="margin-bottom:16px">Yeni Duyuru Gönder</h3>
    <form method="POST" action="{{ route('admin.broadcasts') }}">
        @csrf
        <div class="form-group">
            <label>Başlık</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>Mesaj</label>
            <textarea name="message_text" rows="4" required style="width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);background:var(--cream)"></textarea>
        </div>
        <div class="form-group">
            <label>Hedef</label>
            <select name="target_gender">
                <option value="all">Tüm Kullanıcılar</option>
                <option value="male">Yalnızca Erkekler</option>
                <option value="female">Yalnızca Kadınlar</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Duyuru Gönder</button>
    </form>
</div>

<div class="admin-table-wrap"><table class="admin-table">
    <thead>
        <tr>
            <th>Başlık</th>
            <th>Hedef</th>
            <th>Gönderilen</th>
            <th>Tarih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($broadcasts as $b)
        <tr>
            <td>{{ $b->title }}</td>
            <td>{{ $b->target_gender }}</td>
            <td>{{ $b->sent_count }} kullanıcı</td>
            <td>{{ $b->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table></div>

{{ $broadcasts->links() }}
@endsection
