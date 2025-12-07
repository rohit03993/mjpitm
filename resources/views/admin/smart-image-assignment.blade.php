<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Smart Image Assignment') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-900">🤖 Smart Image Assignment</h3>
                    <p class="text-sm text-purple-700 mt-1">Automatically fetch and assign relevant images based on course/category names</p>
                </div>

                <form action="{{ route('admin.smart-image-assignment.assign') }}" method="POST" class="p-6" id="smartImageForm">
                    @csrf

                    <!-- Type Selection -->
                    <div class="mb-6">
                        <x-input-label for="type" :value="__('Assign Images For *')" />
                        <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="loadItems()">
                            <option value="">Select Type</option>
                            <option value="courses">Courses ({{ $courses->count() }} without images)</option>
                            <option value="categories">Categories ({{ $categories->count() }} without images)</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Items List -->
                    <div id="itemsContainer" class="hidden mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Select Items to Assign Images:</h4>
                            <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Select All</button>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div id="itemsList" class="space-y-2">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div id="quickActions" class="hidden mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800 mb-2"><strong>Quick Action:</strong></p>
                            <form action="{{ route('admin.smart-image-assignment.assign-all') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="type" id="quickActionType" value="">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    🚀 Assign Images to ALL Items ({{ $courses->count() + $categories->count() }} items)
                                </button>
                            </form>
                            <p class="text-xs text-blue-600 mt-2">This will automatically assign images to all courses and categories that don't have images yet.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded hidden">
                            ✨ Assign Images →
                        </button>
                    </div>
                </form>
            </div>

            <!-- How It Works -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✨ How Smart Image Assignment Works</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-green-800">
                    <div>
                        <div class="font-semibold mb-2">1. Keyword Extraction</div>
                        <p>System extracts meaningful keywords from course/category names (e.g., "Agriculture Course" → "agriculture")</p>
                    </div>
                    <div>
                        <div class="font-semibold mb-2">2. Image Search</div>
                        <p>Automatically searches for relevant images using those keywords from free image sources</p>
                    </div>
                    <div>
                        <div class="font-semibold mb-2">3. Auto-Assignment</div>
                        <p>Downloads and assigns the most relevant image to each course/category automatically</p>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-white rounded border border-green-300">
                    <p class="text-sm text-green-900"><strong>💡 Tip:</strong> The system uses intelligent keyword matching to find the most relevant images. You can assign images to selected items or use "Assign to ALL" for bulk assignment.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const courses = @json($courses);
        const categories = @json($categories);

        function loadItems() {
            const type = document.getElementById('type').value;
            const itemsContainer = document.getElementById('itemsContainer');
            const itemsList = document.getElementById('itemsList');
            const submitBtn = document.getElementById('submitBtn');
            const quickActions = document.getElementById('quickActions');
            const quickActionType = document.getElementById('quickActionType');
            
            if (!type) {
                itemsContainer.classList.add('hidden');
                submitBtn.classList.add('hidden');
                quickActions.classList.add('hidden');
                return;
            }
            
            const items = type === 'courses' ? courses : categories;
            
            if (items.length === 0) {
                itemsList.innerHTML = '<p class="text-gray-500 text-center py-4">All items already have images! 🎉</p>';
                itemsContainer.classList.remove('hidden');
                submitBtn.classList.add('hidden');
                quickActions.classList.add('hidden');
                return;
            }
            
            itemsList.innerHTML = '';
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex items-center p-2 hover:bg-white rounded';
                div.innerHTML = `
                    <input type="checkbox" name="items[]" value="${item.id}" id="item_${item.id}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="item_${item.id}" class="ml-3 text-sm text-gray-900 flex-1">
                        <span class="font-medium">${item.name}</span>
                        ${item.code ? `<span class="text-gray-500 ml-2">(${item.code})</span>` : ''}
                    </label>
                `;
                itemsList.appendChild(div);
            });
            
            itemsContainer.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
            quickActions.classList.remove('hidden');
            quickActionType.value = type;
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('input[name="items[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
</x-app-layout>

