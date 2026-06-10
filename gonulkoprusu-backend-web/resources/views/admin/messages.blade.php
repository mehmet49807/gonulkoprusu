@extends('layouts.admin')

@section('title', 'Mesaj Denetimi')

@section('content')
<h1 class="admin-page-title">Mesaj Denetimi</h1>

<div class="admin-table-wrap"><table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Gönderen</th>
            <th>Alıcı</th>
            <th>Mesaj</th>
            <th>Tarih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($messages as $msg)
        <tr>
            <td>{{ $msg->id }}</td>
            <td>{{ $msg->sender->username ?? '-' }}</td>
            <td>{{ $msg->receiver->username ?? '-' }}</td>
            <td>{{ Str::limit($msg->message_text, 60) }}</td>
            <td>{{ $msg->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table></div>

{{ $messages->links() }}
@endsection
