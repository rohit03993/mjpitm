<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Map Excel Columns') }}
            </h2>
            <a href="{{ route('admin.courses.import') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">Step 2: Map Your Excel Columns</h3>
                    <p class="text-sm text-green-700 mt-1">Select which column from your Excel file corresponds to each system field.</p>
                    <p class="text-sm text-gray-600 mt-2"><strong>Institute:</strong> {{ $institute->name }}</p>
                </div>

                <form action="{{ route('admin.courses.import.process') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="institute_id" value="{{ $instituteId }}">
                    <input type="hidden" name="temp_path" value="{{ $tempPath }}">

                    <!-- Field Mappings -->
                    <div class="space-y-4 mb-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Map Excel Columns to System Fields:</h4>
                        
                        @foreach($systemFields as $fieldKey => $fieldLabel)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $fieldLabel }}
                                        @if(strpos($fieldLabel, 'Required') !== false)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    @if($fieldKey === 'tuition_fee')
                                        <p class="text-xs text-gray-500 mt-1">Can be total fee (e.g., "Rs. 11,500") - system will parse it</p>
                                    @endif
                                    @if($fieldKey === 'registration_fee')
                                        <p class="text-xs text-gray-500 mt-1">Optional - if not provided, will be set to 0</p>
                                    @endif
                                    @if($fieldKey === 'duration')
                                        <p class="text-xs text-gray-500 mt-1">Smart parser: Handles "1 Year Program", "6 months", "1 year 6 months" - extracts years and months automatically</p>
                                    @endif
                                </div>
                                <div>
                                    <select name="mappings[{{ $fieldKey }}]" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 mapping-select" 
                                            data-field="{{ $fieldKey }}"
                                            @if(strpos($fieldLabel, 'Required') !== false && $fieldKey !== 'registration_fee') required @endif>
                                        <option value="">-- Select Column --</option>
                                        @foreach($headers as $index => $header)
                                            @php
                                                // Auto-suggest based on header name
                                                $selected = old('mappings.' . $fieldKey);
                                                if (!$selected) {
                                                    $headerLower = strtolower($header);
                                                    if ($fieldKey === 'category_name' && (stripos($headerLower, 'categor') !== false)) {
                                                        $selected = $index;
                                                    } elseif ($fieldKey === 'course_name' && (stripos($headerLower, 'course') !== false || stripos($headerLower, 'title') !== false)) {
                                                        $selected = $index;
                                                    } elseif ($fieldKey === 'tuition_fee' && (stripos($headerLower, 'fee') !== false || stripos($headerLower, 'fees') !== false)) {
                                                        $selected = $index;
                                                    } elseif ($fieldKey === 'duration' && (stripos($headerLower, 'duration') !== false || stripos($headerLower, 'year') !== false || stripos($headerLower, 'month') !== false)) {
                                                        $selected = $index;
                                                    }
                                                }
                                            @endphp
                                            <option value="{{ $index }}" {{ $selected == $index ? 'selected' : '' }}>
                                                Column {{ $index + 1 }}: "{{ $header }}"
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="text-sm text-gray-500 sample-preview" data-field="{{ $fieldKey }}">
                                    @if(isset($sampleData[0]))
                                        @php
                                            $sampleIndex = old('mappings.' . $fieldKey, '');
                                            if (!$sampleIndex && isset($systemFields[$fieldKey])) {
                                                // Try to auto-detect
                                                foreach($headers as $idx => $h) {
                                                    $hLower = strtolower($h);
                                                    if ($fieldKey === 'category_name' && stripos($hLower, 'categor') !== false) {
                                                        $sampleIndex = $idx;
                                                        break;
                                                    } elseif ($fieldKey === 'course_name' && (stripos($hLower, 'course') !== false || stripos($hLower, 'title') !== false)) {
                                                        $sampleIndex = $idx;
                                                        break;
                                                    } elseif ($fieldKey === 'tuition_fee' && (stripos($hLower, 'fee') !== false || stripos($hLower, 'fees') !== false)) {
                                                        $sampleIndex = $idx;
                                                        break;
                                                    } elseif ($fieldKey === 'duration' && (stripos($hLower, 'duration') !== false || stripos($hLower, 'year') !== false || stripos($hLower, 'month') !== false)) {
                                                        $sampleIndex = $idx;
                                                        break;
                                                    }
                                                }
                                            }
                                            $sampleValue = $sampleIndex !== '' && isset($sampleData[0][$sampleIndex]) ? $sampleData[0][$sampleIndex] : '';
                                        @endphp
                                        @if($sampleValue)
                                            <span class="text-gray-600">Sample: <strong>{{ Str::limit($sampleValue, 30) }}</strong></span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Preview Table -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Preview (First 5 Rows):</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Row</th>
                                        @foreach($headers as $index => $header)
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Col {{ $index + 1 }}<br>
                                                <span class="text-xs font-normal text-gray-600">{{ Str::limit($header, 20) }}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sampleData as $rowIndex => $row)
                                        <tr class="{{ $rowIndex % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $rowIndex + 2 }}</td>
                                            @foreach($headers as $colIndex => $header)
                                                <td class="px-4 py-2 text-sm text-gray-600">
                                                    {{ isset($row[$colIndex]) ? Str::limit($row[$colIndex], 30) : '' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.courses.import') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                            Confirm & Import →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sampleData = @json($sampleData);
        
        // Update sample values when dropdown changes
        document.querySelectorAll('.mapping-select').forEach(select => {
            select.addEventListener('change', function() {
                const fieldKey = this.getAttribute('data-field');
                const sampleDiv = document.querySelector(`.sample-preview[data-field="${fieldKey}"]`);
                const selectedIndex = parseInt(this.value);
                
                if (selectedIndex >= 0 && sampleDiv && sampleData[0]) {
                    const value = sampleData[0][selectedIndex];
                    if (value) {
                        sampleDiv.innerHTML = '<span class="text-gray-600">Sample: <strong>' + 
                            (String(value).substring(0, 30)) + '</strong></span>';
                    } else {
                        sampleDiv.innerHTML = '';
                    }
                } else {
                    if (sampleDiv) sampleDiv.innerHTML = '';
                }
            });
            
            // Trigger on page load to show initial samples
            if (this.value) {
                this.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>

