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
                            <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ old('institute_id', session('current_institute_id')) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
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

                        <!-- Duration (Years) -->
                        <div>
                            <x-input-label for="duration_years" :value="__('Duration (Years) *')" />
                            <x-text-input id="duration_years" class="block mt-1 w-full" type="number" name="duration_years" :value="old('duration_years', 3)" required min="1" max="10" />
                            <x-input-error :messages="$errors->get('duration_years')" class="mt-2" />
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
                        <p class="text-sm text-gray-600 mt-1">Set default fees for this course. These will be auto-populated when registering students.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Registration Fee -->
                        <div>
                            <x-input-label for="registration_fee" :value="__('Registration Fee')" />
                            <x-text-input id="registration_fee" class="block mt-1 w-full" type="number" step="0.01" name="registration_fee" :value="old('registration_fee')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('registration_fee')" class="mt-2" />
                        </div>

                        <!-- Entrance Fee -->
                        <div>
                            <x-input-label for="entrance_fee" :value="__('Entrance Fee')" />
                            <x-text-input id="entrance_fee" class="block mt-1 w-full" type="number" step="0.01" name="entrance_fee" :value="old('entrance_fee')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('entrance_fee')" class="mt-2" />
                        </div>

                        <!-- Enrollment Fee -->
                        <div>
                            <x-input-label for="enrollment_fee" :value="__('Enrollment Fee')" />
                            <x-text-input id="enrollment_fee" class="block mt-1 w-full" type="number" step="0.01" name="enrollment_fee" :value="old('enrollment_fee')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('enrollment_fee')" class="mt-2" />
                        </div>

                        <!-- Tuition Fee -->
                        <div>
                            <x-input-label for="tuition_fee" :value="__('Tuition Fee')" />
                            <x-text-input id="tuition_fee" class="block mt-1 w-full" type="number" step="0.01" name="tuition_fee" :value="old('tuition_fee')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('tuition_fee')" class="mt-2" />
                        </div>

                        <!-- Caution Money -->
                        <div>
                            <x-input-label for="caution_money" :value="__('Caution Money')" />
                            <x-text-input id="caution_money" class="block mt-1 w-full" type="number" step="0.01" name="caution_money" :value="old('caution_money')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('caution_money')" class="mt-2" />
                        </div>

                        <!-- Hostel Fee -->
                        <div>
                            <x-input-label for="hostel_fee_amount" :value="__('Hostel Fee')" />
                            <x-text-input id="hostel_fee_amount" class="block mt-1 w-full" type="number" step="0.01" name="hostel_fee_amount" :value="old('hostel_fee_amount')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('hostel_fee_amount')" class="mt-2" />
                        </div>

                        <!-- Late Fee -->
                        <div>
                            <x-input-label for="late_fee" :value="__('Late Fee')" />
                            <x-text-input id="late_fee" class="block mt-1 w-full" type="number" step="0.01" name="late_fee" :value="old('late_fee')" min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('late_fee')" class="mt-2" />
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
</x-app-layout>

