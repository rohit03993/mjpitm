<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Details') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Students
                </a>
                @if(auth()->user() && auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit Status & Roll No.
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Header summary --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        {{-- Photo --}}
                        <div class="flex md:block justify-center items-start">
                            @if($student->photo)
                                <div class="flex flex-col items-center">
                                    <img
                                        src="{{ asset('storage/' . $student->photo) }}"
                                        alt="Photo of {{ $student->name }}"
                                        class="w-28 h-36 md:w-32 md:h-40 rounded-md object-cover object-top border border-gray-300 shadow-sm bg-gray-50"
                                    >
                                    <p class="mt-2 text-xs text-gray-500">Student Photo (Passport Style)</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center text-gray-400 text-sm">
                                    <div class="w-28 h-36 md:w-32 md:h-40 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-md bg-gray-50">
                                        No photo uploaded
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Basic Information</h3>
                            <p class="text-sm text-gray-700"><strong>Name:</strong> {{ $student->name }}</p>
                            <p class="text-sm text-gray-700"><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700"><strong>Mobile:</strong> {{ $student->phone ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700"><strong>Institute:</strong> {{ $student->institute->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700"><strong>Course:</strong> {{ $student->course->name ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Academic & Status</h3>
                            <p class="text-sm text-gray-700">
                                <strong>Registration No:</strong>
                                {{ $student->registration_number ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Roll Number:</strong>
                                {{ $student->roll_number ?? 'Not assigned' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1 flex items-center">
                                <strong class="mr-1">Status:</strong>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($student->status === 'active')
                                        bg-green-100 text-green-800
                                    @elseif($student->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($student->status === 'rejected')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Registered By:</strong> {{ $student->creator->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Registered on {{ $student->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Optional: Qualifications --}}
                    @if($student->qualifications && $student->qualifications->count())
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Qualifications</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-700">Exam</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-700">Board / University</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-700">Year</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-700">Percentage / Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($student->qualifications as $qualification)
                                            <tr>
                                                <td class="px-4 py-2 text-gray-800">{{ $qualification->exam_name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-gray-800">{{ $qualification->board_university ?? '-' }}</td>
                                                <td class="px-4 py-2 text-gray-800">{{ $qualification->year ?? '-' }}</td>
                                                <td class="px-4 py-2 text-gray-800">{{ $qualification->percentage ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="mt-4 flex justify-end space-x-3">
                        <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to List
                        </a>
                        @if(auth()->user() && auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                                Edit Status & Roll No.
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


