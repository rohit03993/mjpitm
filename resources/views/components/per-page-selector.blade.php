@php
    $param = $param ?? 'per_page';
    $options = $options ?? [5, 10];
    $current = request()->query($param, $default ?? 10);
@endphp

<form method="GET" class="mb-3 flex items-center justify-end gap-2 text-sm">
    @foreach(request()->except(['page', 'history_page', $param]) as $key => $value)
        @if(is_array($value))
            @foreach($value as $arrayValue)
                <input type="hidden" name="{{ $key }}[]" value="{{ $arrayValue }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <label for="per-page-{{ $param }}" class="text-gray-600">Show</label>
    <select id="per-page-{{ $param }}"
            name="{{ $param }}"
            class="rounded-md border-gray-300 py-1 text-sm focus:border-indigo-500 focus:ring-indigo-500"
            onchange="this.form.submit()">
        @foreach($options as $option)
            <option value="{{ $option }}" {{ (int)$current === (int)$option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <span class="text-gray-600">per page</span>
</form>
