<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Course') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Please fix the following errors:') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.courses.store') }}">
                @csrf

                <!-- Basic Course Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Basic Course Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Institute -->
                        <div class="md:col-span-2">
                            <x-input-label for="institute_id" :value="__('Institute *')" />
                            <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="filterCategories(this.value)">
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ old('institute_id', session('current_institute_id')) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="md:col-span-2">
                            <x-input-label for="category_id" :value="__('Course Category')" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Category (Optional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-institute-id="{{ $category->institute_id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }} ({{ $category->institute->name ?? '' }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Group this course under a category. Select institute first to filter categories.</p>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Course Name -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Course Name *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="e.g., Bachelor of Computer Applications" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Course Code -->
                        <div>
                            <x-input-label for="code" :value="__('Course Code *')" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required placeholder="e.g., BCA" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div class="md:col-span-2">
                            <x-input-label for="duration_value" :value="__('Duration *')" />
                            <div class="flex gap-2 mt-1">
                                <x-text-input id="duration_value" class="flex-1" type="number" step="0.1" name="duration_value" :value="old('duration_value')" required min="0.1" placeholder="Enter duration" oninput="updateDurationDisplay(); updateFeeLabel(); calculateTotalFee();" />
                                <select id="duration_type" name="duration_type" class="rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateDurationDisplay(); updateFeeLabel(); calculateTotalFee();">
                                    <option value="months" {{ old('duration_type', 'months') == 'months' ? 'selected' : '' }}>Months</option>
                                    <option value="years" {{ old('duration_type') == 'years' ? 'selected' : '' }}>Years</option>
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter duration value and select whether it's in months or years</p>
                            <x-input-error :messages="$errors->get('duration_value')" class="mt-2" />
                            <x-input-error :messages="$errors->get('duration_type')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status *')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Fee Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Course Fee Structure</h3>
                        <p class="text-sm text-gray-600 mt-1">Enter fee per year or total fee - the other will be calculated automatically based on course duration (works for both years and months).</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <!-- Fee Per Year/Month (Dynamic) -->
                            <div>
                                <x-input-label for="tuition_fee" :value="__('Tuition Fee')" />
                                <label id="tuition_fee_label" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tuition Fee (Per Year) ₹
                                </label>
                                <x-text-input id="tuition_fee" class="block mt-1 w-full" type="number" step="0.01" name="tuition_fee" :value="old('tuition_fee')" min="0" placeholder="0.00" oninput="calculateTotalFee(); updateFeeLabel();" />
                                <x-input-error :messages="$errors->get('tuition_fee')" class="mt-2" />
                            </div>

                            <!-- Multiply Symbol -->
                            <div class="hidden md:flex items-center justify-center text-2xl text-gray-400 font-bold pb-2">
                                × <span id="duration_display" class="ml-2">-</span> =
                            </div>

                            <!-- Total Fee (Calculated) -->
                            <div>
                                <x-input-label for="total_course_fee" :value="__('Total Course Fee ₹')" />
                                <x-text-input id="total_course_fee" class="block mt-1 w-full bg-green-50 border-green-300" type="number" step="0.01" min="0" placeholder="0.00" oninput="calculatePerYearFee()" />
                                <p class="mt-1 text-xs text-gray-500">Auto-calculated or enter manually</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Create Course') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Categories data from server
        const categoriesData = @json(isset($categoriesJson) ? json_decode($categoriesJson, true) : []);
        
        // Filter categories based on selected institute
        function filterCategories(instituteId) {
            const categorySelect = document.getElementById('category_id');
            const allOptions = categorySelect.querySelectorAll('option');
            
            // Show all options first
            allOptions.forEach(option => {
                option.style.display = '';
            });
            
            // Filter categories by institute
            if (instituteId) {
                const instituteIdStr = String(instituteId);
                allOptions.forEach(option => {
                    if (option.value) {
                        const optionInstituteId = String(option.dataset.instituteId || '');
                        if (optionInstituteId !== instituteIdStr) {
                            option.style.display = 'none';
                        }
                    }
                });
                
                // Reset selection if current selection is hidden
                if (categorySelect.value) {
                    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                    if (selectedOption && selectedOption.style.display === 'none') {
                        categorySelect.value = '';
                    }
                }
            }
        }
        
        // Update fee label based on duration
        function updateFeeLabel() {
            const durationValueInput = document.getElementById('duration_value');
            const durationTypeSelect = document.getElementById('duration_type');
            const feeLabel = document.getElementById('tuition_fee_label');
            
            if (!feeLabel || !durationValueInput || !durationTypeSelect) {
                return;
            }
            
            const durationValue = parseFloat(durationValueInput.value) || 0;
            const durationType = durationTypeSelect.value;
            
            let totalMonths = 0;
            if (durationType === 'years') {
                totalMonths = durationValue * 12;
            } else {
                totalMonths = durationValue;
            }
            
            // If duration is less than 12 months, show "Per Month", otherwise "Per Year"
            if (totalMonths > 0 && totalMonths < 12) {
                feeLabel.textContent = 'Tuition Fee (Per Month) ₹';
            } else if (totalMonths >= 12) {
                feeLabel.textContent = 'Tuition Fee (Per Year) ₹';
            } else {
                // If no value entered yet, check the type - if months selected, show per month hint
                if (durationType === 'months') {
                    feeLabel.textContent = 'Tuition Fee (Per Month) ₹';
                } else {
                    feeLabel.textContent = 'Tuition Fee (Per Year) ₹';
                }
            }
        }
        
        // Update duration display in fee section
        function updateDurationDisplay() {
            const durationValue = parseFloat(document.getElementById('duration_value').value) || 0;
            const durationType = document.getElementById('duration_type').value;
            const durationDisplay = document.getElementById('duration_display');
            
            if (durationDisplay) {
                let displayText = '-';
                if (durationValue > 0) {
                    if (durationType === 'years') {
                        const totalMonths = durationValue * 12;
                        if (totalMonths < 12) {
                            displayText = totalMonths + ' month' + (totalMonths > 1 ? 's' : '');
                        } else {
                            const years = Math.floor(totalMonths / 12);
                            const months = totalMonths % 12;
                            if (months > 0) {
                                displayText = years + ' year' + (years > 1 ? 's' : '') + ' ' + months + ' month' + (months > 1 ? 's' : '');
                            } else {
                                displayText = years + ' year' + (years > 1 ? 's' : '');
                            }
                        }
                    } else {
                        const totalMonths = durationValue;
                        if (totalMonths < 12) {
                            displayText = totalMonths + ' month' + (totalMonths > 1 ? 's' : '');
                        } else {
                            const years = Math.floor(totalMonths / 12);
                            const months = totalMonths % 12;
                            if (months > 0) {
                                displayText = years + ' year' + (years > 1 ? 's' : '') + ' ' + months + ' month' + (months > 1 ? 's' : '');
                            } else {
                                displayText = years + ' year' + (years > 1 ? 's' : '');
                            }
                        }
                    }
                }
                durationDisplay.textContent = displayText;
            }
            
            // Update fee label when duration changes
            updateFeeLabel();
        }
        
        // Calculate total fee from per-year/month fee
        function calculateTotalFee() {
            const tuitionFee = parseFloat(document.getElementById('tuition_fee').value) || 0;
            const durationValue = parseFloat(document.getElementById('duration_value').value) || 0;
            const durationType = document.getElementById('duration_type').value;
            
            if (tuitionFee <= 0 || durationValue <= 0) {
                document.getElementById('total_course_fee').value = '';
                return;
            }
            
            // Convert duration to total months
            let totalMonths = 0;
            if (durationType === 'years') {
                totalMonths = durationValue * 12;
            } else {
                totalMonths = durationValue;
            }
            
            // Check if we're using per-month or per-year fee
            const feeLabel = document.getElementById('tuition_fee_label');
            const isPerMonth = feeLabel && feeLabel.textContent.includes('Per Month');
            
            let totalFee = 0;
            if (isPerMonth && totalMonths < 12) {
                // Direct multiplication for per-month fees
                totalFee = tuitionFee * totalMonths;
            } else {
                // For per-year fees: Total Fee = (Per Year Fee / 12) × Total Months
                const perMonthFee = tuitionFee / 12;
                totalFee = perMonthFee * totalMonths;
            }
            
            document.getElementById('total_course_fee').value = totalFee > 0 ? totalFee.toFixed(2) : '';
        }
        
        // Calculate per-year/month fee from total fee
        function calculatePerYearFee() {
            const totalFee = parseFloat(document.getElementById('total_course_fee').value) || 0;
            const durationValue = parseFloat(document.getElementById('duration_value').value) || 0;
            const durationType = document.getElementById('duration_type').value;
            
            if (totalFee <= 0 || durationValue <= 0) {
                document.getElementById('tuition_fee').value = '';
                return;
            }
            
            // Convert duration to total months
            let totalMonths = 0;
            if (durationType === 'years') {
                totalMonths = durationValue * 12;
            } else {
                totalMonths = durationValue;
            }
            
            // Check if we should show per-month or per-year
            const feeLabel = document.getElementById('tuition_fee_label');
            const isPerMonth = feeLabel && feeLabel.textContent.includes('Per Month');
            
            let tuitionFee = 0;
            if (isPerMonth && totalMonths < 12) {
                // Direct division for per-month fees
                tuitionFee = totalFee / totalMonths;
            } else {
                // For per-year: Per Year Fee = (Total Fee / Total Months) × 12
                const perMonthFee = totalFee / totalMonths;
                tuitionFee = perMonthFee * 12;
            }
            
            document.getElementById('tuition_fee').value = tuitionFee > 0 ? tuitionFee.toFixed(2) : '';
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const instituteSelect = document.getElementById('institute_id');
            if (instituteSelect && instituteSelect.value) {
                filterCategories(instituteSelect.value);
            }
            
            // Initialize duration display and fee label
            updateDurationDisplay();
            updateFeeLabel();
            
            // Calculate total if per-year fee exists
            const tuitionFee = document.getElementById('tuition_fee');
            if (tuitionFee && tuitionFee.value) {
                calculateTotalFee();
            }
            
            // Update fee label when duration type changes
            const durationType = document.getElementById('duration_type');
            const durationValue = document.getElementById('duration_value');
            if (durationType) {
                durationType.addEventListener('change', updateFeeLabel);
            }
            if (durationValue) {
                durationValue.addEventListener('input', updateFeeLabel);
            }
        });
    </script>
</x-app-layout>

