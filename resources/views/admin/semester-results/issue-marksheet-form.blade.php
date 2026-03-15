<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Issue Marksheet') }}
            </h2>
            <a href="{{ route('admin.semester-results.show', $semesterResult->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="text-sm text-red-800 list-disc list-inside">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-teal-50 border-b border-teal-200">
                    <h3 class="text-lg font-semibold text-teal-900">Semester {{ $semesterResult->semester }} — {{ $semesterResult->academic_year }}</h3>
                    <p class="text-sm text-teal-700 mt-1">{{ $semesterResult->student->name }} · {{ $semesterResult->course->name }}</p>
                    <p class="text-xs text-teal-600 mt-2">Result already published. Set the marksheet issue date to generate the printable PDF.</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.semester-results.issue-marksheet', $semesterResult->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="date_of_issue" class="block text-sm font-medium text-gray-700">Marksheet issue date <span class="text-gray-500">({{ $isOddSem ? 'March' : 'August' }} — printed on marksheet)</span></label>
                            <input type="date" name="date_of_issue" id="date_of_issue"
                                   value="{{ old('date_of_issue', $defaultIssueDate) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                        <div class="mt-8 flex gap-4">
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                                Generate marksheet PDF
                            </button>
                            <a href="{{ route('admin.semester-results.show', $semesterResult->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
