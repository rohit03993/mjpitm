<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Publish Result') }}
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
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Semester {{ $semesterResult->semester }} — {{ $semesterResult->academic_year }}</h3>
                    <p class="text-sm text-blue-700 mt-1">{{ $semesterResult->student->name }} · {{ $semesterResult->course->name }}</p>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">
                        Set the <strong>result declaration date</strong>. The result will be published and visible to the student. The marksheet can be generated and printed later from this page (separate step).
                        For {{ $isOddSem ? 'odd' : 'even' }} semesters, use {{ $isOddSem ? 'February' : 'July' }}.
                    </p>

                    <form action="{{ route('admin.semester-results.publish', $semesterResult->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="result_declaration_date" class="block text-sm font-medium text-gray-700">Result declaration date <span class="text-gray-500">({{ $isOddSem ? 'February' : 'July' }})</span></label>
                            <input type="date" name="result_declaration_date" id="result_declaration_date"
                                   value="{{ old('result_declaration_date', $defaultResultDate) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div class="mt-8 flex gap-4">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Publish result
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
