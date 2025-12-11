<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Institutes') }}
            </h2>
            <a href="{{ route('superadmin.institutes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                + Create Institute
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
                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden lg:block overflow-hidden">
                        <table class="w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admins</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($institutes as $institute)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $institute->name }}">
                                                {{ $institute->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <div class="max-w-xs truncate" title="{{ $institute->domain }}">
                                                {{ $institute->domain }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $institute->students_count }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $institute->courses_count }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $institute->admins_count }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $institute->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($institute->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('superadmin.institutes.show', $institute->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs whitespace-nowrap">View</a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('superadmin.institutes.edit', $institute->id) }}" class="text-blue-600 hover:text-blue-900 text-xs whitespace-nowrap">Edit</a>
                                                @if($institute->students_count == 0 && $institute->courses_count == 0)
                                                    <span class="text-gray-300">|</span>
                                                    <form action="{{ route('superadmin.institutes.destroy', $institute->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this institute?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs whitespace-nowrap">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No institutes found. <a href="{{ route('superadmin.institutes.create') }}" class="text-indigo-600 hover:text-indigo-900">Create a new institute</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View (visible on mobile/tablet, hidden on desktop) -->
                    <div class="block lg:hidden space-y-4">
                        @forelse($institutes as $institute)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $institute->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $institute->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($institute->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">Domain: {{ $institute->domain }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Students:</span>
                                        <span class="text-sm text-gray-900">{{ $institute->students_count }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Courses:</span>
                                        <span class="text-sm text-gray-900">{{ $institute->courses_count }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Admins:</span>
                                        <span class="text-sm text-gray-900">{{ $institute->admins_count }}</span>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('superadmin.institutes.show', $institute->id) }}" 
                                           class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                            View
                                        </a>
                                        <a href="{{ route('superadmin.institutes.edit', $institute->id) }}" 
                                           class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
                                            Edit
                                        </a>
                                        @if($institute->students_count == 0 && $institute->courses_count == 0)
                                            <form action="{{ route('superadmin.institutes.destroy', $institute->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this institute?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500">
                                No institutes found. <a href="{{ route('superadmin.institutes.create') }}" class="text-indigo-600 hover:text-indigo-900">Create a new institute</a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($institutes->hasPages())
                        <div class="mt-6">
                            {{ $institutes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

