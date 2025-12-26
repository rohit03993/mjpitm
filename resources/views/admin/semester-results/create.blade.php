<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Generate Semester Result') }}
            </h2>
            <a href="{{ route('admin.students.show', $student->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Student
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Student Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Student Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Student Name</p>
                        <p class="font-semibold text-gray-900">{{ $student->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Course</p>
                        <p class="font-semibold text-gray-900">{{ $student->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Semester</p>
                        <p class="font-semibold text-gray-900">Semester {{ $nextSemester }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.students.generate-semester-result.store', $student->id) }}" id="result-form">
                @csrf
                <input type="hidden" name="semester" value="{{ $nextSemester }}">
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-green-50 border-b border-green-200">
                        <h3 class="text-lg font-semibold text-green-900">Enter Marks for Semester {{ $nextSemester }}</h3>
                        <p class="text-sm text-green-700 mt-1">Academic Year: {{ $academicYear }}</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Code</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Theory (Max)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Theory (Obtained) *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Practical (Max)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Practical (Obtained) *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subjects as $index => $subject)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $subject->code }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $subject->theory_marks ?? 0 }}</td>
                                            <td class="px-4 py-3">
                                                <input type="number" 
                                                       step="0.01" 
                                                       name="subjects[{{ $index }}][theory_marks_obtained]" 
                                                       class="theory-obtained block w-full rounded-md border-gray-300" 
                                                       required 
                                                       min="0" 
                                                       max="{{ $subject->theory_marks ?? 0 }}"
                                                       data-theory-max="{{ $subject->theory_marks ?? 0 }}"
                                                       value="{{ old("subjects.$index.theory_marks_obtained", '') }}"
                                                       onchange="validateAndCalculate(this)">
                                                <input type="hidden" name="subjects[{{ $index }}][subject_id]" value="{{ $subject->id }}">
                                                <span class="text-xs text-red-600 theory-error hidden"></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $subject->practical_marks ?? 0 }}</td>
                                            <td class="px-4 py-3">
                                                <input type="number" 
                                                       step="0.01" 
                                                       name="subjects[{{ $index }}][practical_marks_obtained]" 
                                                       class="practical-obtained block w-full rounded-md border-gray-300" 
                                                       required 
                                                       min="0" 
                                                       max="{{ $subject->practical_marks ?? 0 }}"
                                                       data-practical-max="{{ $subject->practical_marks ?? 0 }}"
                                                       value="{{ old("subjects.$index.practical_marks_obtained", '') }}"
                                                       onchange="validateAndCalculate(this)">
                                                <span class="text-xs text-red-600 practical-error hidden"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="subject-total text-sm font-semibold text-gray-900">0</span>
                                                <span class="text-sm text-gray-500">/ {{ $subject->total_marks ?? 0 }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-900">Overall:</td>
                                        <td class="px-4 py-3">
                                            <span class="overall-total text-sm font-semibold text-gray-900">0</span>
                                            <span class="text-sm text-gray-500">/ <span id="overall-max">{{ $subjects->sum('total_marks') }}</span></span>
                                            <div class="mt-1">
                                                <span class="overall-percentage text-sm font-semibold text-gray-900">0%</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.students.show', $student->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Submit Result') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateAndCalculate(input) {
            const row = input.closest('tr');
            const theoryInput = row.querySelector('.theory-obtained');
            const practicalInput = row.querySelector('.practical-obtained');
            const totalSpan = row.querySelector('.subject-total');
            const theoryError = row.querySelector('.theory-error');
            const practicalError = row.querySelector('.practical-error');
            
            // Get max values
            const theoryMax = parseFloat(theoryInput.getAttribute('data-theory-max')) || 0;
            const practicalMax = parseFloat(practicalInput.getAttribute('data-practical-max')) || 0;
            
            // Get entered values
            let theory = parseFloat(theoryInput.value) || 0;
            let practical = parseFloat(practicalInput.value) || 0;
            
            // Validate theory marks
            if (theory > theoryMax) {
                theoryInput.value = theoryMax;
                theory = theoryMax;
                if (theoryError) {
                    theoryError.textContent = `Max: ${theoryMax}`;
                    theoryError.classList.remove('hidden');
                    setTimeout(() => theoryError.classList.add('hidden'), 3000);
                }
            } else {
                if (theoryError) {
                    theoryError.classList.add('hidden');
                }
            }
            
            // Validate practical marks
            if (practical > practicalMax) {
                practicalInput.value = practicalMax;
                practical = practicalMax;
                if (practicalError) {
                    practicalError.textContent = `Max: ${practicalMax}`;
                    practicalError.classList.remove('hidden');
                    setTimeout(() => practicalError.classList.add('hidden'), 3000);
                }
            } else {
                if (practicalError) {
                    practicalError.classList.add('hidden');
                }
            }
            
            // Calculate total
            const total = theory + practical;
            totalSpan.textContent = total.toFixed(2);
            
            // Calculate overall
            calculateOverall();
        }

        function calculateOverall() {
            let totalObtained = 0;
            let totalMax = 0;

            document.querySelectorAll('tbody tr').forEach(row => {
                const theoryInput = row.querySelector('.theory-obtained');
                const practicalInput = row.querySelector('.practical-obtained');
                if (theoryInput && practicalInput) {
                    const theory = parseFloat(theoryInput.value) || 0;
                    const practical = parseFloat(practicalInput.value) || 0;
                    totalObtained += theory + practical;
                }
                
                const maxText = row.querySelector('.subject-total')?.nextElementSibling?.textContent;
                if (maxText) {
                    const max = parseFloat(maxText.split('/')[1]?.trim()) || 0;
                    totalMax += max;
                }
            });

            const overallTotal = document.querySelector('.overall-total');
            const overallPercentage = document.querySelector('.overall-percentage');

            if (overallTotal) {
                overallTotal.textContent = totalObtained.toFixed(2);
            }

            if (totalMax > 0) {
                const percentage = (totalObtained / totalMax) * 100;
                if (overallPercentage) {
                    overallPercentage.textContent = percentage.toFixed(2) + '%';
                }
            }
        }
    </script>
</x-app-layout>

