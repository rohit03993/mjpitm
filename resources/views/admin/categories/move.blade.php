<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Move Category to Another Institute') }}
            </h2>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Warning Box -->
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important: This action will move the following:</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>1 Category:</strong> {{ $category->name }}</li>
                                <li><strong>{{ $category->courses->count() }} Course(s)</strong> in this category</li>
                                <li><strong>{{ $studentsCount }} Student(s)</strong> enrolled in those courses</li>
                            </ul>
                            <p class="mt-3 font-semibold">All courses and students will be moved to the new institute. This action cannot be undone easily.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Move Category: {{ $category->name }}</h3>
                    <p class="text-sm text-blue-700 mt-1">Select the target institute where this category should be moved.</p>
                </div>
                
                <form action="{{ route('admin.categories.move.process', $category) }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="current_institute_id" value="{{ $category->institute_id }}">

                    <div class="space-y-6">
                        <!-- Current Institute (Read-only) -->
                        <div>
                            <x-input-label for="current_institute" :value="__('Current Institute')" />
                            <input type="text" id="current_institute" value="{{ $category->institute->name }}" class="block mt-1 w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly disabled>
                            <p class="mt-1 text-xs text-gray-500">This is the institute where the category currently belongs.</p>
                        </div>

                        <!-- Target Institute -->
                        <div>
                            <x-input-label for="target_institute_id" :value="__('Target Institute *')" />
                            <select id="target_institute_id" name="target_institute_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Target Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ old('target_institute_id') == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the institute where you want to move this category and all its courses.</p>
                            <x-input-error :messages="$errors->get('target_institute_id')" class="mt-2" />
                        </div>

                        <!-- Preview Section -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">What will be moved:</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Category:</span>
                                    <span class="font-medium">{{ $category->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Courses:</span>
                                    <span class="font-medium">{{ $category->courses->count() }} course(s)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Students:</span>
                                    <span class="font-medium">{{ $studentsCount }} student(s)</span>
                                </div>
                                <div class="pt-2 border-t border-gray-300 mt-2">
                                    <p class="text-xs text-gray-500 italic">Note: All students enrolled in courses of this category will also be moved to the new institute.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded" onclick="return confirm('Are you sure you want to move this category and all its courses to the new institute? This will also move {{ $studentsCount }} student(s).')">
                            Confirm Move
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

