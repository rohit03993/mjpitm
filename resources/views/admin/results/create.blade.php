<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Result Entry') }}
            </h2>
            <a href="{{ route('admin.results.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Results
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

            <form method="POST" action="{{ route('admin.results.store') }}">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Result Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student -->
                        <div class="md:col-span-2">
                            <x-input-label for="student_id" :value="__('Student *')" />
                            <select id="student_id" name="student_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} - {{ $student->course->name ?? 'N/A' }} ({{ $student->roll_number ?? $student->registration_number ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Subject -->
                        <div>
                            <x-input-label for="subject_id" :value="__('Subject *')" />
                            <select id="subject_id" name="subject_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} - {{ $subject->course->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                        </div>

                        <!-- Exam Type -->
                        <div>
                            <x-input-label for="exam_type" :value="__('Exam Type *')" />
                            <select id="exam_type" name="exam_type" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Exam Type</option>
                                @foreach($examTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('exam_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('exam_type')" class="mt-2" />
                        </div>

                        <!-- Semester -->
                        <div>
                            <x-input-label for="semester" :value="__('Semester *')" />
                            <x-text-input id="semester" class="block mt-1 w-full" type="text" name="semester" :value="old('semester')" required placeholder="e.g., 1, 2, 3..." />
                            <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                        </div>

                        <!-- Academic Year -->
                        <div>
                            <x-input-label for="academic_year" :value="__('Academic Year *')" />
                            <x-text-input id="academic_year" class="block mt-1 w-full" type="text" name="academic_year" :value="old('academic_year', date('Y') . '-' . (date('Y') + 1))" required placeholder="e.g., 2024-25" />
                            <x-input-error :messages="$errors->get('academic_year')" class="mt-2" />
                        </div>

                        <!-- Marks Obtained -->
                        <div>
                            <x-input-label for="marks_obtained" :value="__('Marks Obtained *')" />
                            <x-text-input id="marks_obtained" class="block mt-1 w-full" type="number" step="0.01" name="marks_obtained" :value="old('marks_obtained')" required min="0" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('marks_obtained')" class="mt-2" />
                        </div>

                        <!-- Total Marks -->
                        <div>
                            <x-input-label for="total_marks" :value="__('Total Marks *')" />
                            <x-text-input id="total_marks" class="block mt-1 w-full" type="number" step="0.01" name="total_marks" :value="old('total_marks', 100)" required min="0" placeholder="100.00" />
                            <x-input-error :messages="$errors->get('total_marks')" class="mt-2" />
                        </div>

                        <!-- Remarks -->
                        <div class="md:col-span-2">
                            <x-input-label for="remarks" :value="__('Remarks')" />
                            <textarea id="remarks" name="remarks" rows="3" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.results.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Create Result Entry') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

