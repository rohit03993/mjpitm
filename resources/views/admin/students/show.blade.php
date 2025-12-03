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

                    {{-- Fee Payment Section --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Fee Payments</h3>
                            <a href="{{ route('admin.fees.create') }}?student_id={{ $student->id }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Payment
                            </a>
                        </div>

                        {{-- Fee Summary Cards --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <p class="text-xs font-medium text-blue-600 uppercase">Total Course Fee</p>
                                <p class="text-xl font-bold text-blue-900">₹{{ number_format($totalCourseFee, 2) }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                <p class="text-xs font-medium text-green-600 uppercase">Verified Paid</p>
                                <p class="text-xl font-bold text-green-900">₹{{ number_format($verifiedPayments, 2) }}</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                                <p class="text-xs font-medium text-yellow-600 uppercase">Pending Verification</p>
                                <p class="text-xl font-bold text-yellow-900">₹{{ number_format($pendingPayments, 2) }}</p>
                            </div>
                            <div class="rounded-lg p-4 border {{ $remainingBalance > 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100' }}">
                                <p class="text-xs font-medium {{ $remainingBalance > 0 ? 'text-red-600' : 'text-emerald-600' }} uppercase">Balance Due</p>
                                <p class="text-xl font-bold {{ $remainingBalance > 0 ? 'text-red-900' : 'text-emerald-900' }}">₹{{ number_format($remainingBalance, 2) }}</p>
                            </div>
                        </div>

                        {{-- Payment History Table --}}
                        @if($student->fees->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Date</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Type</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Amount</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Transaction ID</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Added By</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($student->fees->sortByDesc('payment_date') as $fee)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-800">{{ $fee->payment_date?->format('d M Y') ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-800">{{ ucfirst($fee->payment_type) }}</td>
                                        <td class="px-4 py-3 text-gray-800 font-semibold">₹{{ number_format($fee->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $fee->transaction_id ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $fee->markedBy->name ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($fee->status === 'verified')
                                                    bg-green-100 text-green-800
                                                @elseif($fee->status === 'pending_verification')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $fee->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($fee->status === 'pending_verification' && auth()->user()->isSuperAdmin())
                                                <button type="button" onclick="openApproveModal({{ $fee->id }}, {{ $fee->amount }})" class="text-green-600 hover:text-green-800 font-medium text-xs mr-2">
                                                    Approve
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.fees.show', $fee->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm">No payment records yet.</p>
                            <a href="{{ route('admin.fees.create') }}?student_id={{ $student->id }}" class="mt-3 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add first payment
                            </a>
                        </div>
                        @endif
                    </div>

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
    @if(auth()->user()->isSuperAdmin())
    <!-- Approval Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Approve Payment</h3>
                    <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Amount: <strong class="text-lg text-green-600">₹<span id="modalAmount">0</span></strong></p>
                    </div>
                    <div class="mb-4">
                        <label for="modal_transaction_id" class="block text-sm font-medium text-gray-700 mb-1">Transaction ID / Receipt No. *</label>
                        <input type="text" id="modal_transaction_id" name="transaction_id" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter transaction ID">
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Approve Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(feeId, amount) {
            document.getElementById('approveForm').action = '/admin/fees/' + feeId + '/verify';
            document.getElementById('modalAmount').textContent = parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('modal_transaction_id').value = '';
            document.getElementById('approveModal').classList.remove('hidden');
        }
        
        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    </script>
    @endif
</x-app-layout>


