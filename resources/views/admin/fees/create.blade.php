<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Fee Entry') }}
            </h2>
            <a href="{{ route('admin.fees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Fees
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

            <form method="POST" action="{{ route('admin.fees.store') }}">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Fee Payment Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student -->
                        <div class="md:col-span-2">
                            <x-input-label for="student_id" :value="__('Student *')" />
                            <select id="student_id" name="student_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateStudentInfo()">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" 
                                        data-course="{{ $student->course->name ?? 'N/A' }}"
                                        data-fee="{{ $student->course->tuition_fee ?? 0 }}"
                                        {{ (old('student_id', $selectedStudentId ?? '') == $student->id) ? 'selected' : '' }}>
                                        {{ $student->name }} - {{ $student->course->name ?? 'N/A' }} ({{ $student->roll_number ?? $student->registration_number ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                            
                            <!-- Student Info Display -->
                            <div id="student-info" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-100 {{ isset($selectedStudent) ? '' : 'hidden' }}">
                                <p class="text-sm text-blue-800">
                                    <strong>Course:</strong> <span id="student-course">{{ $selectedStudent->course->name ?? '—' }}</span>
                                </p>
                                <p class="text-sm text-blue-800">
                                    <strong>Total Course Fee:</strong> ₹<span id="student-fee">{{ number_format($selectedStudent->course->tuition_fee ?? 0, 2) }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Hidden Payment Type (always tuition) -->
                        <input type="hidden" name="payment_type" value="tuition">

                        <!-- Amount -->
                        <div>
                            <x-input-label for="amount" :value="__('Amount Received (₹) *')" />
                            <x-text-input id="amount" class="block mt-1 w-full text-lg font-semibold" type="number" step="0.01" name="amount" :value="old('amount')" required min="1" placeholder="Enter amount" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <x-input-label for="payment_date" :value="__('Payment Date *')" />
                            <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" :value="old('payment_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>

                        <!-- Remarks -->
                        <div class="md:col-span-2">
                            <x-input-label for="remarks" :value="__('Remarks (Optional)')" />
                            <textarea id="remarks" name="remarks" rows="2" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any notes about this payment...">{{ old('remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>
                        
                        <div class="md:col-span-2 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong>Note:</strong> This payment will be marked as <span class="font-semibold">Pending Verification</span>. Admin will verify and add Transaction ID.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.fees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Create Fee Entry') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function updateStudentInfo() {
            const select = document.getElementById('student_id');
            const infoDiv = document.getElementById('student-info');
            const courseSpan = document.getElementById('student-course');
            const feeSpan = document.getElementById('student-fee');
            
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const course = selectedOption.dataset.course || '—';
                const fee = parseFloat(selectedOption.dataset.fee) || 0;
                
                courseSpan.textContent = course;
                feeSpan.textContent = fee.toLocaleString('en-IN', {minimumFractionDigits: 2});
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateStudentInfo();
        });
    </script>
</x-app-layout>

