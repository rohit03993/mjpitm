<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Result Verification Queue') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.results.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    All Results
                </a>
                <a href="{{ route('admin.results.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Add Result Entry
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
                    <!-- Search -->
                    <form method="GET" action="{{ route('admin.results.verification-queue') }}" class="mb-6">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search by student name, roll number, or subject name..."
                                    class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Search
                            </button>
                            <a href="{{ route('admin.results.verification-queue') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>

                    <!-- Pending Results Table (desktop) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-yellow-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($results as $result)
                                    <tr class="hover:bg-yellow-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $result->student->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $result->student->roll_number ?? $result->student->registration_number ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $result->subject->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">Sem {{ $result->semester }} - {{ $result->academic_year }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($result->exam_type ?? 'N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-semibold">{{ $result->marks_obtained ?? 'N/A' }}</span>
                                            <span class="text-gray-500">/ {{ $result->total_marks ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                            {{ $result->percentage ?? 'N/A' }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                            {{ $result->grade ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $result->uploadedBy->name ?? 'N/A' }}
                                            <div class="text-xs text-gray-400">{{ display_date($result->created_at) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('admin.results.show', $result->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                <form action="{{ route('admin.results.verify', $result->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to verify and publish this result?');">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Verify</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No pending results for verification. Great job! 🎉
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pending Results Cards (mobile/tablet) -->
                    <div class="lg:hidden space-y-4">
                        @forelse($results as $result)
                            <div class="rounded-xl border border-yellow-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-base font-semibold text-gray-900">{{ $result->student->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $result->student->roll_number ?? $result->student->registration_number ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </div>
                                <div class="mt-3 space-y-1 text-sm">
                                    <p><span class="text-gray-500">Subject:</span> <span class="font-medium text-gray-900">{{ $result->subject->name ?? 'N/A' }}</span></p>
                                    <p><span class="text-gray-500">Exam:</span> {{ ucfirst($result->exam_type ?? 'N/A') }} | Sem {{ $result->semester }} - {{ $result->academic_year }}</p>
                                    <p><span class="text-gray-500">Marks:</span> <span class="font-semibold">{{ $result->marks_obtained ?? 'N/A' }}</span> / {{ $result->total_marks ?? 'N/A' }}</p>
                                    <p><span class="text-gray-500">Uploaded By:</span> {{ $result->uploadedBy->name ?? 'N/A' }} ({{ display_date($result->created_at) }})</p>
                                </div>
                                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <a href="{{ route('admin.results.show', $result->id) }}" class="inline-flex justify-center rounded-lg bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                                        View
                                    </a>
                                    <form action="{{ route('admin.results.verify', $result->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to verify and publish this result?');">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-700">
                                            Verify
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500">
                                No pending results for verification. Great job! 🎉
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($results->hasPages())
                        <div class="mt-6">
                            <x-per-page-selector :default="10" />
                            {{ $results->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

