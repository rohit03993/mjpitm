<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Details') }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back
                </a>
                @if($student->status === 'active' && $student->roll_number)
                    <a href="{{ route('admin.documents.view.idcard', $student->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        View ID Card
                    </a>
                @endif
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
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
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

                    {{-- Documents Section --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Photo --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Photograph</h4>
                                @if($student->photo)
                                    <a href="{{ asset('storage/' . $student->photo) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo" class="w-full h-32 object-cover rounded border border-gray-300 hover:opacity-80 transition">
                                    </a>
                                    <a href="{{ asset('storage/' . $student->photo) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline">View Full Size</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Signature --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Signature</h4>
                                @if($student->signature)
                                    <a href="{{ asset('storage/' . $student->signature) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $student->signature) }}" alt="Signature" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                    </a>
                                    <a href="{{ asset('storage/' . $student->signature) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline">View Full Size</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Aadhar Front --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Aadhar Front</h4>
                                @if($student->aadhar_front)
                                    <a href="{{ asset('storage/' . $student->aadhar_front) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $student->aadhar_front) }}" alt="Aadhar Front" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                    </a>
                                    <a href="{{ asset('storage/' . $student->aadhar_front) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline">View Full Size</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Aadhar Back --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Aadhar Back</h4>
                                @if($student->aadhar_back)
                                    <a href="{{ asset('storage/' . $student->aadhar_back) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $student->aadhar_back) }}" alt="Aadhar Back" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                    </a>
                                    <a href="{{ asset('storage/' . $student->aadhar_back) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline">View Full Size</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Academic Certificates Section --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Certificates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Class 10th Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Class 10th Certificate</h4>
                                @if($student->certificate_class_10th)
                                    @php
                                        $isPdf = pathinfo($student->certificate_class_10th, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_class_10th) }}" alt="Class 10th Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_class_10th) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Class 12th Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Class 12th Certificate</h4>
                                @if($student->certificate_class_12th)
                                    @php
                                        $isPdf = pathinfo($student->certificate_class_12th, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_class_12th) }}" alt="Class 12th Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_class_12th) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Graduation Certificate --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Graduation Certificate</h4>
                                @if($student->certificate_graduation)
                                    @php
                                        $isPdf = pathinfo($student->certificate_graduation, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_graduation) }}" alt="Graduation Certificate" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_graduation) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Other Certificates --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Other Certificates</h4>
                                @if($student->certificate_others)
                                    @php
                                        $isPdf = pathinfo($student->certificate_others, PATHINFO_EXTENSION) === 'pdf';
                                    @endphp
                                    @if($isPdf)
                                        <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="block">
                                            <div class="w-full h-32 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-50 transition">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-600">PDF Document</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $student->certificate_others) }}" alt="Other Certificates" class="w-full h-32 object-contain rounded border border-gray-300 bg-white hover:opacity-80 transition">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $student->certificate_others) }}" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline block">View / Download</a>
                                @else
                                    <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded bg-white text-gray-400 text-xs">
                                        Not uploaded
                                    </div>
                                @endif
                            </div>
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
                    <div class="mt-4 flex flex-wrap justify-end gap-3">
                        <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to List
                        </a>
                        <a href="{{ route('admin.documents.view.registration', $student->id) }}" target="_blank" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Registration Form
                        </a>
                        @if($student->status === 'active' && $student->roll_number)
                            <a href="{{ route('admin.documents.view.idcard', $student->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                View ID Card
                            </a>
                        @endif
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


