<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Semester Result Details') }}
            </h2>
            <a href="{{ route('admin.students.show', $semesterResult->student_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Student
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

            <!-- Result Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Result Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Student</p>
                        <p class="font-semibold text-gray-900">{{ $semesterResult->student->name }}</p>
                        <p class="text-xs text-gray-500">{{ $semesterResult->student->roll_number ?? $semesterResult->student->registration_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Course</p>
                        <p class="font-semibold text-gray-900">{{ $semesterResult->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Semester</p>
                        <p class="font-semibold text-gray-900">Semester {{ $semesterResult->semester }}</p>
                        <p class="text-xs text-gray-500">{{ $semesterResult->academic_year }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Overall Percentage</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $semesterResult->overall_percentage ?? 0 }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $semesterResult->status === 'published' ? 'bg-green-100 text-green-800' : 
                               ($semesterResult->status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $semesterResult->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Subject Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">Subject-wise Results</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Theory</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Practical</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($semesterResult->results as $result)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $result->subject->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $result->subject->code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $result->theory_marks_obtained ?? 0 }} / {{ $result->subject->theory_marks ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $result->practical_marks_obtained ?? 0 }} / {{ $result->subject->practical_marks ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <span class="font-semibold">{{ $result->marks_obtained ?? 0 }}</span>
                                            <span class="text-gray-500">/ {{ $result->total_marks ?? 0 }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-900">Overall:</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                        {{ $semesterResult->total_marks_obtained }} / {{ $semesterResult->total_max_marks }}
                                        <div class="mt-1">
                                            <span class="text-sm font-semibold text-gray-900">{{ $semesterResult->overall_percentage }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($semesterResult->status !== 'published')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="flex gap-4">
                            <form action="{{ route('admin.semester-results.publish', $semesterResult->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to publish this result? Once published, it will be visible to the student.');">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    ✓ Publish Result
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="flex gap-4">
                            <a href="{{ route('admin.semester-results.download', $semesterResult->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                📄 Download PDF
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Published on: {{ $semesterResult->published_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

