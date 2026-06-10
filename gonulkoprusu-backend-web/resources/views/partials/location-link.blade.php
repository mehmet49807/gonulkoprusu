@php
    $country = $country ?? 'Türkiye';
    $routeParams = ['country' => $country, 'city' => $city];
    if (!empty($district)) {
        $routeParams['district'] = $district;
    }
    $label = app(\App\Services\LocationDataService::class)->formatLabel($country, $city, $district ?? null);
@endphp
<a href="{{ route('locations.users', $routeParams) }}" class="location-link {{ $class ?? '' }}">
    {{ $label }}
</a>
