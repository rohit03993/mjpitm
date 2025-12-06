<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Management') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.fees.verification-queue') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Verification Queue
                </a>
                @endif
                <a href="{{ route('admin.fees.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Add Payment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Fees Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Mode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added By</th>
                                    @if(auth()->user()->isSuperAdmin())
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($fees as $fee)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            ₹{{ number_format($fee->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $fee->payment_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($fee->payment_mode ?? 'offline') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $fee->payment_date ? $fee->payment_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $fee->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                                   ($fee->status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $fee->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $fee->markedBy->name ?? 'N/A' }}
                                        </td>
                                        @if(auth()->user()->isSuperAdmin())
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button type="button" onclick="openViewModal({{ $fee->id }}, {{ $fee->amount }}, '{{ $fee->payment_mode ?? 'offline' }}', '{{ $fee->payment_date ? $fee->payment_date->format('d M Y') : 'N/A' }}', '{{ $fee->status }}', '{{ addslashes($fee->markedBy->name ?? 'N/A') }}', '{{ $fee->created_at ? $fee->created_at->format('d M Y, h:i A') : 'N/A' }}', '{{ addslashes($fee->verifiedBy->name ?? 'N/A') }}', '{{ $fee->verified_at ? $fee->verified_at->format('d M Y, h:i A') : 'N/A' }}', '{{ addslashes($fee->approved_by_name ?? 'N/A') }}')" class="text-blue-600 hover:text-blue-900 mr-3">
                                                View
                                            </button>
                                            @if($fee->status === 'pending_verification')
                                                <button type="button" onclick="openApproveModal({{ $fee->id }}, {{ $fee->amount }})" class="text-green-600 hover:text-green-900 font-semibold">
                                                    Approve
                                                </button>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isSuperAdmin() ? '6' : '5' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No payment entries yet. <a href="{{ route('admin.fees.create') }}" class="text-indigo-600 hover:text-indigo-900">Add your first payment</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($fees->hasPages())
                        <div class="mt-6">
                            {{ $fees->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Details</h3>
                    <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900" id="viewAmount">—</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Mode</dt>
                        <dd class="mt-1 text-sm text-gray-900" id="viewPaymentMode">—</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                        <dd class="mt-1 text-sm text-gray-900" id="viewPaymentDate">—</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm" id="viewStatus">—</dd>
                    </div>
                    <div class="border-t pt-3">
                        <dt class="text-sm font-medium text-gray-500">Added By</dt>
                        <dd class="mt-1 text-sm text-gray-900" id="viewAddedBy">—</dd>
                        <dd class="text-xs text-gray-500" id="viewCreatedAt">—</dd>
                    </div>
                    <div id="viewApprovedSection" class="border-t pt-3 hidden">
                        <dt class="text-sm font-medium text-gray-500">Approved By (Admin)</dt>
                        <dd class="mt-1 text-sm text-gray-900" id="viewVerifiedBy">—</dd>
                        <dd class="text-xs text-gray-500" id="viewVerifiedAt">—</dd>
                        <dt class="text-sm font-medium text-gray-500 mt-2">Payment Received/Approved By</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold" id="viewApprovedByName">—</dd>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                        <label for="modal_approved_by_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Person Who Approved/Received Payment *</label>
                        <input type="text" id="modal_approved_by_name" name="approved_by_name" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter name of person who received/approved the payment">
                        <p class="mt-1 text-xs text-gray-500">Enter the name of the person (cashier/staff) who physically received or approved this payment</p>
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
        function openViewModal(feeId, amount, paymentMode, paymentDate, status, addedBy, createdAt, verifiedBy, verifiedAt, approvedByName) {
            // Set amount
            document.getElementById('viewAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2});
            
            // Set payment mode
            const modeBadge = paymentMode === 'online' 
                ? '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">' + paymentMode.charAt(0).toUpperCase() + paymentMode.slice(1) + '</span>'
                : '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">' + paymentMode.charAt(0).toUpperCase() + paymentMode.slice(1) + '</span>';
            document.getElementById('viewPaymentMode').innerHTML = modeBadge;
            
            // Set payment date
            document.getElementById('viewPaymentDate').textContent = paymentDate;
            
            // Set status
            let statusClass = 'bg-gray-100 text-gray-800';
            if (status === 'verified') statusClass = 'bg-green-100 text-green-800';
            else if (status === 'pending_verification') statusClass = 'bg-yellow-100 text-yellow-800';
            else if (status === 'rejected') statusClass = 'bg-red-100 text-red-800';
            
            const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            document.getElementById('viewStatus').innerHTML = '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' + statusClass + '">' + statusText + '</span>';
            
            // Set added by info
            document.getElementById('viewAddedBy').textContent = addedBy;
            document.getElementById('viewCreatedAt').textContent = 'Added on: ' + createdAt;
            
            // Set approved info (if verified)
            if (status === 'verified' && verifiedBy !== 'N/A') {
                document.getElementById('viewApprovedSection').classList.remove('hidden');
                document.getElementById('viewVerifiedBy').textContent = verifiedBy;
                document.getElementById('viewVerifiedAt').textContent = 'Approved on: ' + verifiedAt;
                document.getElementById('viewApprovedByName').textContent = approvedByName !== 'N/A' ? approvedByName : '—';
            } else {
                document.getElementById('viewApprovedSection').classList.add('hidden');
            }
            
            document.getElementById('viewModal').classList.remove('hidden');
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }
        
        function openApproveModal(feeId, amount) {
            document.getElementById('approveForm').action = '/admin/fees/' + feeId + '/verify';
            document.getElementById('modalAmount').textContent = parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('modal_approved_by_name').value = '';
            document.getElementById('approveModal').classList.remove('hidden');
        }
        
        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }
        
        // Close modals when clicking outside
        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewModal();
            }
        });
        
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    </script>
    @endif
</x-app-layout>

