<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Payment Details') }}
            </h2>
            <a href="{{ route('admin.fees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Fees
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Fee Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">Payment Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Student</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $fee->student->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $fee->student->roll_number ?? $fee->student->registration_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fee->student->course->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $fee->student->institute->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-lg text-gray-900 font-bold">₹{{ number_format($fee->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($fee->payment_type ?? 'N/A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Semester</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fee->semester ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Mode</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $fee->payment_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($fee->payment_mode ?? 'offline') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fee->payment_date ? $fee->payment_date->format('d M Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $fee->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                   ($fee->status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $fee->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($fee->remarks)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Remarks</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $fee->remarks }}</dd>
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
                        <dt class="text-sm font-medium text-gray-500">Marked By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fee->markedBy->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $fee->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                    @if($fee->verified_by)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified By (Admin)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $fee->verifiedBy->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $fee->verified_at ? $fee->verified_at->format('d M Y, h:i A') : 'N/A' }}</dd>
                    </div>
                    @endif
                    @if($fee->approved_by_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Received/Approved By</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $fee->approved_by_name }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions - Only for Super Admin -->
            @if($fee->status === 'pending_verification' && auth()->user()->isSuperAdmin())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                    
                    <!-- Approve Form -->
                    <form action="{{ route('admin.fees.verify', $fee->id) }}" method="POST" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        @csrf
                        <div class="mb-4">
                            <label for="approved_by_name" class="block text-sm font-medium text-green-900 mb-2">Name of Person Who Approved/Received Payment *</label>
                            <input type="text" id="approved_by_name" name="approved_by_name" required
                                class="block w-full rounded-md border-green-300 bg-white text-gray-900 focus:border-green-500 focus:ring-green-500"
                                placeholder="Enter name of person who received/approved the payment">
                            <p class="mt-1 text-xs text-green-700">Enter the name of the person (cashier/staff) who physically received or approved this payment</p>
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            ✓ Approve Payment
                        </button>
                    </form>

                    <!-- Reject Section -->
                    <div class="border-t pt-4">
                        <button type="button" onclick="document.getElementById('reject-form').classList.toggle('hidden')" class="text-red-600 hover:text-red-800 font-medium text-sm">
                            ✗ Reject this payment instead
                        </button>
                    </div>

                    <!-- Reject Form (Hidden by default) -->
                    <div id="reject-form" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form action="{{ route('admin.fees.reject', $fee->id) }}" method="POST">
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
            @elseif($fee->status === 'pending_verification')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    This payment is <strong>pending verification</strong> by Admin.
                </p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

