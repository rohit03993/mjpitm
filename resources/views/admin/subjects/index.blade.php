<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subjects') }}
            </h2>
            <a href="{{ route('admin.subjects.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                + Add New Subject
            </a>
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
                    <!-- Search and Filters -->
                    <form method="GET" action="{{ route('admin.subjects.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by subject name or code..."
                                class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div class="w-full md:w-48">
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <select
                                id="course_id"
                                name="course_id"
                                class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->institute->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-32">
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <select
                                id="semester"
                                name="semester"
                                class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Sem {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('admin.subjects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>

                    <!-- Subjects Table (desktop) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($subjects as $subject)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $subject->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subject->course->name ?? 'N/A' }}
                                            <span class="text-xs text-gray-400">({{ $subject->course->institute->name ?? 'N/A' }})</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Semester {{ $subject->semester }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subject->credits ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($subject->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <span class="mx-2">|</span>
                                            <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            @if($subject->results->count() == 0)
                                                <span class="mx-2">|</span>
                                                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No subjects found. <a href="{{ route('admin.subjects.create') }}" class="text-indigo-600 hover:text-indigo-900">Add a new subject</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Subjects Cards (mobile/tablet) -->
                    <div class="lg:hidden space-y-4">
                        @forelse($subjects as $subject)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-indigo-700">{{ $subject->code }}</p>
                                        <p class="text-base font-semibold text-gray-900">{{ $subject->name }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($subject->status) }}
                                    </span>
                                </div>
                                <div class="mt-3 space-y-1 text-sm text-gray-700">
                                    <p><span class="text-gray-500">Course:</span> {{ $subject->course->name ?? 'N/A' }}</p>
                                    <p><span class="text-gray-500">Institute:</span> {{ $subject->course->institute->name ?? 'N/A' }}</p>
                                    <p><span class="text-gray-500">Semester:</span> {{ $subject->semester }}</p>
                                    <p><span class="text-gray-500">Credits:</span> {{ $subject->credits ?? 'N/A' }}</p>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="inline-flex justify-center rounded-lg bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">View</a>
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="inline-flex justify-center rounded-lg bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100">Edit</a>
                                    @if($subject->results->count() == 0)
                                        <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?');" class="col-span-2 sm:col-span-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-lg bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500">
                                No subjects found. <a href="{{ route('admin.subjects.create') }}" class="text-indigo-600 hover:text-indigo-900">Add a new subject</a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($subjects->hasPages())
                        <div class="mt-6">
                            <x-per-page-selector :default="10" />
                            {{ $subjects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

