<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Result Details') }}
            </h2>
            <a href="{{ route('admin.results.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Results
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Result Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Result Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Student</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $result->student->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $result->student->roll_number ?? $result->student->registration_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $result->student->course->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $result->student->institute->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subject</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $result->subject->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $result->subject->code ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Exam Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($result->exam_type ?? 'N/A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Semester</dt>
                        <dd class="mt-1 text-sm text-gray-900">Semester {{ $result->semester ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $result->academic_year ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Marks</dt>
                        <dd class="mt-1 text-lg text-gray-900 font-bold">
                            {{ $result->marks_obtained ?? 'N/A' }} / {{ $result->total_marks ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Percentage</dt>
                        <dd class="mt-1 text-lg text-gray-900 font-bold">{{ $result->percentage ?? 'N/A' }}%</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Grade</dt>
                        <dd class="mt-1 text-2xl text-gray-900 font-bold">{{ $result->grade ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $result->status === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($result->status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($result->remarks)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Remarks</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $result->remarks }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Verification Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">Verification Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uploaded By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $result->uploadedBy->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $result->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                    @if($result->verified_by)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $result->verifiedBy->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $result->verified_at ? $result->verified_at->format('d M Y, h:i A') : 'N/A' }}</dd>
                    </div>
                    @endif
                    @if($result->published_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Published At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $result->published_at->format('d M Y, h:i A') }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($result->status === 'pending_verification')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex gap-4">
                        <form action="{{ route('admin.results.verify', $result->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to verify and publish this result?');">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                ✓ Verify & Publish
                            </button>
                        </form>
                        <button type="button" onclick="document.getElementById('reject-form').classList.toggle('hidden')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            ✗ Reject Result
                        </button>
                    </div>

                    <!-- Reject Form (Hidden by default) -->
                    <div id="reject-form" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form action="{{ route('admin.results.reject', $result->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="rejection_remarks" class="block text-sm font-medium text-red-900 mb-2">Rejection Reason *</label>
                                <textarea id="rejection_remarks" name="rejection_remarks" rows="3" class="block w-full rounded-md border-red-300 bg-white text-gray-900 focus:border-red-500 focus:ring-red-500" required placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="document.getElementById('reject-form').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

