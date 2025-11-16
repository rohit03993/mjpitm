<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Update Student Status & Roll Number') }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Students
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        {{ __('Please fix the following errors:') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Summary -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Details</h3>
                            <p class="text-sm text-gray-700"><strong>Name:</strong> {{ $student->name }}</p>
                            <p class="text-sm text-gray-700"><strong>Institute:</strong> {{ $student->institute->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700"><strong>Course:</strong> {{ $student->course->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700"><strong>Registration No:</strong> {{ $student->registration_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Status</h3>
                            <p class="text-sm text-gray-700">
                                <strong>Status:</strong>
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
                            <p class="text-sm text-gray-700 mt-2"><strong>Roll Number:</strong> {{ $student->roll_number ?? 'Not assigned' }}</p>
                            <p class="text-sm text-gray-700 mt-2"><strong>Created By:</strong> {{ $student->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.students.update', $student->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Roll Number -->
                            <div>
                                <x-input-label for="roll_number" :value="__('Roll Number')" />
                                <x-text-input
                                    id="roll_number"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="roll_number"
                                    :value="old('roll_number', $student->roll_number)"
                                />
                                <x-input-error :messages="$errors->get('roll_number')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Leave blank while status is Pending/Rejected. Required when setting status to Active.
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status *')" />
                                <select
                                    id="status"
                                    name="status"
                                    class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $student->status) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-4">
                           <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


