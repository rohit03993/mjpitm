<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Courses') }}
            </h2>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.courses.import') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    📥 Bulk Import
                </a>
                <a href="{{ route('admin.smart-image-assignment') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    ✨ Smart Image Assignment
                </a>
                <a href="{{ route('admin.bulk-image-upload') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    📸 Manual Upload
                </a>
                <a href="{{ route('admin.courses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Add New Course
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="search" placeholder="Search by course name, code, or institute..." class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Courses Card View (All Screens) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($courses as $course)
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="text-base font-semibold text-gray-900 line-clamp-2">{{ $course->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full flex-shrink-0 {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-medium text-indigo-600 mb-3">Code: {{ $course->code }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Category:</span>
                                        <span class="text-sm">
                                            @if($course->category)
                                                <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ $course->category->name }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-20 flex-shrink-0">Institute:</span>
                                        <span class="text-sm text-gray-900 flex-1">{{ $course->institute->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-500 mr-2">Duration:</span>
                                            <span class="text-sm font-medium text-gray-900">{{ $course->formatted_duration }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-500 mr-2">Students:</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $course->students_count }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.courses.show', $course->id) }}" 
                                           class="flex-1 px-3 py-2 text-xs font-medium text-center text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition">
                                            View
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" 
                                           class="flex-1 px-3 py-2 text-xs font-medium text-center text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition">
                                            Edit
                                        </a>
                                        @if($course->students_count == 0)
                                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-3 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new course.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        + Add New Course
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($courses->hasPages())
                        <div class="mt-6">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

