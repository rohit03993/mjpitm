<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admins') }}
            </h2>
            <a href="{{ route('superadmin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                + Create Admin
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden lg:block overflow-hidden">
                        <table class="w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($admins as $admin)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $admin->name }}">
                                                {{ $admin->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <div class="max-w-xs truncate" title="{{ $admin->email }}">
                                                {{ $admin->email }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($admin->institute)
                                                <div class="max-w-xs truncate" title="{{ $admin->institute->name }}">
                                                    {{ $admin->institute->name }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">All institutes</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $admin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($admin->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium">
                                            <a href="{{ route('superadmin.users.edit', $admin) }}"
                                               class="text-indigo-600 hover:text-indigo-900 text-xs whitespace-nowrap">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No admins found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View (visible on mobile/tablet, hidden on desktop) -->
                    <div class="block lg:hidden space-y-4">
                        @forelse($admins as $admin)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $admin->name }}</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                {{ $admin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($admin->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Email:</span>
                                        <span class="text-sm text-gray-900 break-all">{{ $admin->email }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-xs font-medium text-gray-500 w-24 flex-shrink-0">Institute:</span>
                                        <span class="text-sm text-gray-900">
                                            @if($admin->institute)
                                                {{ $admin->institute->name }}
                                            @else
                                                <span class="text-xs text-gray-400">All institutes</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('superadmin.users.edit', $admin) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500">
                                No admins found.
                            </div>
                        @endforelse
                    </div>

                    @if($admins->hasPages())
                        <div class="mt-6">
                            {{ $admins->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


