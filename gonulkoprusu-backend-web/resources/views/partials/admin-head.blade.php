@php
    $assetBase = rtrim(config('app.asset_url') ?: config('app.url'), '/');
@endphp
<link rel="icon" href="{{ $assetBase }}/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ $assetBase }}/css/admin.css?v=admin-online-status-1">
