<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('System Reset') }}
            </h2>
            <a href="{{ route('superadmin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Warning Banner -->
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-red-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">Danger Zone</h3>
                        <p class="text-sm text-red-700 mt-1">
                            Actions on this page are <strong>irreversible</strong>. Data cannot be recovered once deleted. 
                            Please ensure you have a backup before proceeding.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Data Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current System Data</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $counts['students'] }}</p>
                            <p class="text-sm text-gray-600">Students</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $counts['guests'] }}</p>
                            <p class="text-sm text-gray-600">Guests</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $counts['courses'] }}</p>
                            <p class="text-sm text-gray-600">Courses</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $counts['fees'] }}</p>
                            <p class="text-sm text-gray-600">Fee Records</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reset Options -->
            <div class="space-y-6">
                <!-- Reset Students Only -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-orange-800">Reset Students Only</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Deletes all students, their fees, results, and qualifications. 
                                    <strong>Courses and Guests will remain.</strong>
                                </p>
                            </div>
                            <button type="button" onclick="openModal('resetStudentsModal')" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Reset Students
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reset Guests Only -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-800">Reset Guests Only</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Deletes all Guest (Channel Partner) accounts. 
                                    <strong>Students and Courses will remain.</strong>
                                </p>
                            </div>
                            <button type="button" onclick="openModal('resetGuestsModal')" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Reset Guests
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reset Everything -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                    <div class="p-6 bg-red-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-red-800">⚠️ Reset ALL Data</h3>
                                <p class="text-sm text-red-700 mt-1">
                                    <strong>COMPLETE SYSTEM RESET:</strong> Deletes ALL students, guests, courses, categories, subjects, fees, and results.
                                    Only Admin accounts and Institutes will remain.
                                </p>
                            </div>
                            <button type="button" onclick="openModal('resetAllModal')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Reset Everything
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Students Modal -->
    <div id="resetStudentsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Reset Students</h3>
                <p class="text-sm text-gray-600 mb-4">This will delete <strong>{{ $counts['students'] }} students</strong> and all their related data.</p>
                
                <form action="{{ route('superadmin.system-reset.students') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <strong>RESET STUDENTS</strong> to confirm:</label>
                        <input type="text" name="confirmation" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="RESET STUDENTS">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('resetStudentsModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Reset Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Guests Modal -->
    <div id="resetGuestsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Reset Guests</h3>
                <p class="text-sm text-gray-600 mb-4">This will delete <strong>{{ $counts['guests'] }} guest accounts</strong>.</p>
                
                <form action="{{ route('superadmin.system-reset.guests') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <strong>RESET GUESTS</strong> to confirm:</label>
                        <input type="text" name="confirmation" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                            placeholder="RESET GUESTS">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('resetGuestsModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            Reset Guests
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset All Modal -->
    <div id="resetAllModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="text-center mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-red-800 text-center mb-2">⚠️ COMPLETE SYSTEM RESET</h3>
                <p class="text-sm text-gray-600 mb-4 text-center">
                    This will delete <strong>ALL</strong> data. This action is <strong>IRREVERSIBLE</strong>.
                </p>
                
                <form action="{{ route('superadmin.system-reset.all') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <strong>RESET ALL DATA</strong> to confirm:</label>
                        <input type="text" name="confirmation" required
                            class="block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="RESET ALL DATA">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('resetAllModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reset Everything
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>

