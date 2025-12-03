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

            <form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
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

                        <!-- Duration (Years) -->
                        <div>
                            <x-input-label for="duration_years" :value="__('Duration (Years) *')" />
                            <x-text-input id="duration_years" class="block mt-1 w-full" type="number" name="duration_years" :value="old('duration_years', $course->duration_years)" required min="1" max="10" oninput="updateDurationDisplay(); calculateTotalFee();" />
                            <x-input-error :messages="$errors->get('duration_years')" class="mt-2" />
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
                    </div>
                </div>

                <!-- Fee Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Course Fee Structure</h3>
                        <p class="text-sm text-gray-600 mt-1">Enter fee per year or total fee - the other will be calculated automatically based on course duration.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <!-- Fee Per Year -->
                            <div>
                                <x-input-label for="tuition_fee" :value="__('Tuition Fee (Per Year) ₹')" />
                                <x-text-input id="tuition_fee" class="block mt-1 w-full" type="number" step="0.01" name="tuition_fee" :value="old('tuition_fee', $course->tuition_fee)" min="0" placeholder="0.00" oninput="calculateTotalFee()" />
                                <x-input-error :messages="$errors->get('tuition_fee')" class="mt-2" />
                            </div>

                            <!-- Multiply Symbol -->
                            <div class="hidden md:flex items-center justify-center text-2xl text-gray-400 font-bold pb-2">
                                × <span id="duration_display">{{ $course->duration_years }}</span> years =
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
        
        // Update duration display in fee section
        function updateDurationDisplay() {
            const duration = document.getElementById('duration_years').value || 3;
            const durationDisplay = document.getElementById('duration_display');
            if (durationDisplay) {
                durationDisplay.textContent = duration;
            }
        }
        
        // Calculate total fee from per-year fee
        function calculateTotalFee() {
            const perYearFee = parseFloat(document.getElementById('tuition_fee').value) || 0;
            const duration = parseInt(document.getElementById('duration_years').value) || 3;
            const totalFee = perYearFee * duration;
            document.getElementById('total_course_fee').value = totalFee > 0 ? totalFee.toFixed(2) : '';
        }
        
        // Calculate per-year fee from total fee
        function calculatePerYearFee() {
            const totalFee = parseFloat(document.getElementById('total_course_fee').value) || 0;
            const duration = parseInt(document.getElementById('duration_years').value) || 3;
            const perYearFee = duration > 0 ? totalFee / duration : 0;
            document.getElementById('tuition_fee').value = perYearFee > 0 ? perYearFee.toFixed(2) : '';
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const instituteSelect = document.getElementById('institute_id');
            if (instituteSelect && instituteSelect.value) {
                filterCategories(instituteSelect.value);
            }
            
            // Initialize duration display
            updateDurationDisplay();
            
            // Calculate total if per-year fee exists
            const tuitionFee = document.getElementById('tuition_fee');
            if (tuitionFee && tuitionFee.value) {
                calculateTotalFee();
            }
        });
    </script>
</x-app-layout>

