<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Subjects - Semester ') . $semester }}
            </h2>
            <a href="{{ route('admin.courses.show', $course->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Course
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">
                        Course: {{ $course->name }} | Semester: {{ $semester }}
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.courses.semester.subjects.store', [$course->id, $semester]) }}" id="subjects-form">
                    @csrf

                    <div class="p-6">
                        <div class="mb-4">
                            <button type="button" onclick="addSubjectRow()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                + Add Subject
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="subjects-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Name *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Code *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Theory Marks *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Practical Marks *</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Marks</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="subjects-tbody">
                                    @if($subjects->count() > 0)
                                        @foreach($subjects as $index => $subject)
                                            <tr class="subject-row">
                                                <td class="px-4 py-3">
                                                    <input type="text" name="subjects[{{ $index }}][name]" value="{{ $subject->name }}" class="block w-full rounded-md border-gray-300" required>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" name="subjects[{{ $index }}][code]" value="{{ $subject->code }}" class="block w-full rounded-md border-gray-300" required>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" step="0.01" name="subjects[{{ $index }}][theory_marks]" value="{{ $subject->theory_marks }}" class="theory-marks block w-full rounded-md border-gray-300" required min="0" onchange="calculateTotal(this)">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" step="0.01" name="subjects[{{ $index }}][practical_marks]" value="{{ $subject->practical_marks }}" class="practical-marks block w-full rounded-md border-gray-300" required min="0" onchange="calculateTotal(this)">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" class="total-marks block w-full rounded-md border-gray-300 bg-gray-100" readonly value="{{ $subject->total_marks ?? ($subject->theory_marks + $subject->practical_marks) }}">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-900">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="subject-row">
                                            <td class="px-4 py-3">
                                                <input type="text" name="subjects[0][name]" class="block w-full rounded-md border-gray-300" required placeholder="e.g., Mathematics">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" name="subjects[0][code]" class="block w-full rounded-md border-gray-300" required placeholder="e.g., MATH101">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" name="subjects[0][theory_marks]" class="theory-marks block w-full rounded-md border-gray-300" required min="0" placeholder="70" onchange="calculateTotal(this)">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" name="subjects[0][practical_marks]" class="practical-marks block w-full rounded-md border-gray-300" required min="0" placeholder="30" onchange="calculateTotal(this)">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" class="total-marks block w-full rounded-md border-gray-300 bg-gray-100" readonly value="0">
                                            </td>
                                            <td class="px-4 py-3">
                                                <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-900">Remove</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.courses.show', $course->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Save Subjects') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = {{ $subjects->count() > 0 ? $subjects->count() : 1 }};

        function addSubjectRow() {
            const tbody = document.getElementById('subjects-tbody');
            const row = document.createElement('tr');
            row.className = 'subject-row';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <input type="text" name="subjects[${rowIndex}][name]" class="block w-full rounded-md border-gray-300" required placeholder="Subject Name">
                </td>
                <td class="px-4 py-3">
                    <input type="text" name="subjects[${rowIndex}][code]" class="block w-full rounded-md border-gray-300" required placeholder="Subject Code">
                </td>
                <td class="px-4 py-3">
                    <input type="number" step="0.01" name="subjects[${rowIndex}][theory_marks]" class="theory-marks block w-full rounded-md border-gray-300" required min="0" placeholder="70" onchange="calculateTotal(this)">
                </td>
                <td class="px-4 py-3">
                    <input type="number" step="0.01" name="subjects[${rowIndex}][practical_marks]" class="practical-marks block w-full rounded-md border-gray-300" required min="0" placeholder="30" onchange="calculateTotal(this)">
                </td>
                <td class="px-4 py-3">
                    <input type="text" class="total-marks block w-full rounded-md border-gray-300 bg-gray-100" readonly value="0">
                </td>
                <td class="px-4 py-3">
                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-900">Remove</button>
                </td>
            `;
            tbody.appendChild(row);
            rowIndex++;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            if (document.querySelectorAll('.subject-row').length > 1) {
                row.remove();
            } else {
                alert('At least one subject is required.');
            }
        }

        function calculateTotal(input) {
            const row = input.closest('tr');
            const theoryInput = row.querySelector('.theory-marks');
            const practicalInput = row.querySelector('.practical-marks');
            const totalInput = row.querySelector('.total-marks');
            
            const theory = parseFloat(theoryInput.value) || 0;
            const practical = parseFloat(practicalInput.value) || 0;
            totalInput.value = (theory + practical).toFixed(2);
        }
    </script>
</x-app-layout>

