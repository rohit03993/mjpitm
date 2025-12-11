<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Institute Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.institutes.edit', $institute->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Institute
                </a>
                <a href="{{ route('superadmin.institutes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Institutes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Basic Institute Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Institute Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Institute Name</dt>
                        <dd class="mt-1 text-lg text-gray-900 font-semibold">{{ $institute->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Domain</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $institute->domain }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $institute->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($institute->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($institute->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $institute->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $institute->students_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Courses</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $institute->courses_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Admins</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $institute->admins_count }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses List (if any) -->
            @if($institute->courses->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-purple-50 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-900">Courses ({{ $institute->courses->count() }})</h3>
                </div>
                <div class="p-6">
                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden lg:block overflow-hidden">
                        <table class="w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($institute->courses->take(10) as $course)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $course->code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $course->name }}">
                                                {{ $course->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $course->formatted_duration }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View (visible on mobile/tablet, hidden on desktop) -->
                    <div class="block lg:hidden space-y-4">
                        @foreach($institute->courses->take(10) as $course)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $course->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">Code: {{ $course->code }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Duration:</span>
                                        <span class="text-sm text-gray-900">{{ $course->formatted_duration }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

