<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Course') }}
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

            <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                                    <option value="{{ $institute->id }}" {{ old('institute_id', $course->institute_id) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
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
                                    <option value="{{ $category->id }}" data-institute-id="{{ $category->institute_id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }} ({{ $category->institute->name ?? '' }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Group this course under a category. Select institute first to filter categories.</p>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Course Name -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Course Name *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $course->name)" required placeholder="e.g., Bachelor of Computer Applications" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Course Code -->
                        <div>
                            <x-input-label for="code" :value="__('Course Code *')" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $course->code)" required placeholder="e.g., BCA" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div class="md:col-span-2">
                            <x-input-label for="duration_value" :value="__('Duration *')" />
                            <div class="flex gap-2 mt-1">
                                <x-text-input id="duration_value" class="flex-1" type="number" step="0.1" name="duration_value" :value="old('duration_value', $durationValue ?? ($course->duration_months ?? 0))" required min="0.1" placeholder="Enter duration" oninput="updateDurationDisplay();" />
                                <select id="duration_type" name="duration_type" class="rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateDurationDisplay();">
                                    <option value="months" {{ old('duration_type', $durationType ?? 'months') == 'months' ? 'selected' : '' }}>Months</option>
                                    <option value="years" {{ old('duration_type', $durationType ?? 'months') == 'years' ? 'selected' : '' }}>Years</option>
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
                                <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $course->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Course Image -->
                        <div class="md:col-span-2">
                            <x-input-label for="image" :value="__('Course Image')" />
                            @if($course->image)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="Current Course Image" class="w-48 h-48 object-cover rounded-lg border border-gray-300">
                                </div>
                            @endif
                            <input id="image" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="image" accept="image/*" onchange="previewCourseImage(this)" />
                            <div id="image_preview" class="mt-4" style="display: none;">
                                <p class="text-sm text-gray-600 mb-2">New Image Preview:</p>
                                <img id="image_preview_img" src="" alt="Course Image Preview" class="w-48 h-48 object-cover rounded-lg border border-gray-300">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload an image related to this course (Max 2MB, JPG/PNG/GIF). Leave empty to keep current image.</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Fee Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Course Fee Structure</h3>
                        <p class="text-sm text-gray-600 mt-1">Enter the total tuition fee for the entire course duration. Registration fee can be customized per course.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tuition Fee (Total for entire course) -->
                            <div>
                                <x-input-label for="tuition_fee" :value="__('Total Tuition Fee ₹')" />
                                <p id="tuition_fee_hint" class="text-xs text-gray-500 mb-1">Total tuition fee for the entire course ({{ $course->formatted_duration }})</p>
                                <x-text-input id="tuition_fee" class="block mt-1 w-full" type="number" step="0.01" name="tuition_fee" :value="old('tuition_fee', $course->tuition_fee)" min="0" placeholder="0.00" oninput="updateTotalFeeDisplay();" />
                                <x-input-error :messages="$errors->get('tuition_fee')" class="mt-2" />
                            </div>

                            <!-- Registration Fee -->
                            <div>
                                <x-input-label for="registration_fee" :value="__('Registration Fee ₹')" />
                                <p class="text-xs text-gray-500 mb-1">One-time registration fee (default: ₹1,000)</p>
                                <x-text-input id="registration_fee" class="block mt-1 w-full" type="number" step="0.01" name="registration_fee" :value="old('registration_fee', $registrationFee ?? 1000)" min="0" placeholder="1000.00" oninput="updateTotalFeeDisplay();" />
                                <x-input-error :messages="$errors->get('registration_fee')" class="mt-2" />
                            </div>
                        </div>
                        
                        <!-- Total Fee Display (Read-only) -->
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Course Fee (Tuition + Registration):</span>
                                <span id="total_fee_display" class="text-lg font-bold text-green-900">
                                    ₹{{ number_format(($course->tuition_fee ?? 0) + ($registrationFee ?? 1000), 2) }}
                                </span>
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
                        {{ __('Update Course') }}
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
        
        // Update total fee display when fees change
        function updateTotalFeeDisplay() {
            const tuitionFee = parseFloat(document.getElementById('tuition_fee').value) || 0;
            const registrationFee = parseFloat(document.getElementById('registration_fee').value) || 1000;
            const totalFee = tuitionFee + registrationFee;
            
            const totalFeeDisplay = document.getElementById('total_fee_display');
            if (totalFeeDisplay) {
                totalFeeDisplay.textContent = '₹' + totalFee.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
        
        // Update fee label based on duration (kept for compatibility but not used for calculation)
        function updateFeeLabel() {
            // This function is kept for compatibility but fee calculation is now direct
            updateTotalFeeDisplay();
        }
        
        // Update duration display and helper text dynamically
        function updateDurationDisplay() {
            const durationValue = parseFloat(document.getElementById('duration_value').value) || 0;
            const durationType = document.getElementById('duration_type').value;
            const tuitionFeeHint = document.getElementById('tuition_fee_hint');
            
            if (!tuitionFeeHint) return;
            
            let displayText = '';
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
                    // months
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
            } else {
                displayText = '{{ $course->formatted_duration }}';
            }
            
            tuitionFeeHint.textContent = 'Total tuition fee for the entire course (' + displayText + ')';
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const instituteSelect = document.getElementById('institute_id');
            if (instituteSelect && instituteSelect.value) {
                filterCategories(instituteSelect.value);
            }
            
            // Initialize duration display
            updateDurationDisplay();
            
            // Initialize total fee display
            updateTotalFeeDisplay();
            
            // Update total fee when fees change
            const tuitionFee = document.getElementById('tuition_fee');
            const registrationFee = document.getElementById('registration_fee');
            if (tuitionFee) {
                tuitionFee.addEventListener('input', updateTotalFeeDisplay);
            }
            if (registrationFee) {
                registrationFee.addEventListener('input', updateTotalFeeDisplay);
            }
        });

        function previewCourseImage(input) {
            const preview = document.getElementById('image_preview');
            const previewImg = document.getElementById('image_preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</x-app-layout>

