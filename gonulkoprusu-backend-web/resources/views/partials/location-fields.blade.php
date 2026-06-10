@php
    $country = old('country', $country ?? 'Türkiye');
    $city = old('city', $city ?? '');
    $district = old('district', $district ?? '');
@endphp

<div class="location-box" data-location-picker
     data-country="{{ $country }}"
     data-city="{{ $city }}"
     data-district="{{ $district }}">
    <select name="country" class="loc-country" required>
        <option value="">Ülke</option>
    </select>
    <select name="city" class="loc-city" required disabled>
        <option value="">Şehir</option>
    </select>
    <div class="loc-district-wrap" hidden>
        <select name="district" class="loc-district">
            <option value="">İlçe</option>
        </select>
    </div>
</div>
@error('country') <small class="form-error">{{ $message }}</small> @enderror
@error('city') <small class="form-error">{{ $message }}</small> @enderror
@error('district') <small class="form-error">{{ $message }}</small> @enderror
