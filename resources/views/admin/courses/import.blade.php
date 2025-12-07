<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bulk Import Courses') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Step 1: Upload Excel File</h3>
                    <p class="text-sm text-blue-700 mt-1">Upload your Excel file (.xlsx or .xls). The system will detect columns and let you map them to our fields.</p>
                </div>
                <form action="{{ route('admin.courses.import.preview') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Institute Selection -->
                        <div>
                            <x-input-label for="institute_id" :value="__('Select Institute *')" />
                            <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ old('institute_id', session('current_institute_id')) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the institute for which you want to import courses</p>
                            <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                        </div>

                        <!-- Excel File Upload -->
                        <div>
                            <x-input-label for="excel_file" :value="__('Excel File *')" />
                            <input id="excel_file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="excel_file" accept=".xlsx,.xls" required />
                            <p class="mt-1 text-xs text-gray-500">Upload Excel file (.xlsx or .xls format, Max 10MB)</p>
                            <x-input-error :messages="$errors->get('excel_file')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            Next: Map Fields →
                        </button>
                    </div>
                </form>
            </div>

            <!-- Instructions -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">📋 Excel File Requirements</h3>
                <div class="space-y-2 text-sm text-yellow-800">
                    <p><strong>Required Columns:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Category Name - The category this course belongs to</li>
                        <li>Course Name - Name of the course</li>
                        <li>Tuition Fee - Tuition fee amount</li>
                        <li>Registration Fee - Registration fee amount</li>
                    </ul>
                    <p class="mt-3"><strong>Optional Columns:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Duration Years - Course duration in years</li>
                        <li>Duration Months - Course duration in months</li>
                        <li>Description - Course description</li>
                        <li>Status - active or inactive (default: active)</li>
                    </ul>
                    <p class="mt-3"><strong>Note:</strong> The system will automatically create categories if they don't exist. Course codes will be auto-generated.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

