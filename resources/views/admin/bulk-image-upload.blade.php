<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bulk Image Upload') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900">📸 Upload Images for Courses or Categories</h3>
                    <p class="text-sm text-blue-700 mt-1">Upload multiple images at once and assign them to courses or categories</p>
                </div>

                <form action="{{ route('admin.bulk-image-upload.upload') }}" method="POST" enctype="multipart/form-data" class="p-6" id="bulkUploadForm">
                    @csrf

                    <!-- Type Selection -->
                    <div class="mb-6">
                        <x-input-label for="type" :value="__('Upload Images For *')" />
                        <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateItemList()">
                            <option value="">Select Type</option>
                            <option value="courses">Courses</option>
                            <option value="categories">Categories</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Image Upload Area -->
                    <div class="mb-6">
                        <x-input-label for="images" :value="__('Select Images *')" />
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required onchange="handleFileSelect(event)">
                        <p class="mt-1 text-xs text-gray-500">You can select multiple images at once (Max 2MB per image)</p>
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                    </div>

                    <!-- Image Mapping Table -->
                    <div id="mappingContainer" class="hidden">
                        <h4 class="font-semibold text-gray-900 mb-4">Map Images to Items:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">File Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Select Item</th>
                                    </tr>
                                </thead>
                                <tbody id="mappingTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded hidden">
                            Upload Images →
                        </button>
                    </div>
                </form>
            </div>

            <!-- Instructions -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">📋 Instructions</h3>
                <div class="space-y-2 text-sm text-yellow-800">
                    <p><strong>Step 1:</strong> Select whether you want to upload images for "Courses" or "Categories"</p>
                    <p><strong>Step 2:</strong> Select multiple image files (you can select all at once using Ctrl+Click or Cmd+Click)</p>
                    <p><strong>Step 3:</strong> Map each image to the corresponding course or category using the dropdown</p>
                    <p><strong>Step 4:</strong> Click "Upload Images" to process</p>
                    <p class="mt-3"><strong>Tips:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Name your image files similar to course/category names for easier matching</li>
                        <li>Supported formats: JPEG, PNG, JPG, GIF</li>
                        <li>Maximum file size: 2MB per image</li>
                        <li>If an item already has an image, it will be replaced</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const courses = @json($courses);
        const categories = @json($categories);
        let selectedFiles = [];

        function updateItemList() {
            const type = document.getElementById('type').value;
            const mappingContainer = document.getElementById('mappingContainer');
            const submitBtn = document.getElementById('submitBtn');
            
            if (type && selectedFiles.length > 0) {
                mappingContainer.classList.remove('hidden');
                submitBtn.classList.remove('hidden');
                populateMappingTable();
            } else {
                mappingContainer.classList.add('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function handleFileSelect(event) {
            selectedFiles = Array.from(event.target.files);
            updateItemList();
        }

        function populateMappingTable() {
            const tbody = document.getElementById('mappingTableBody');
            const type = document.getElementById('type').value;
            const items = type === 'courses' ? courses : categories;
            
            tbody.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                // Image preview
                const imgCell = document.createElement('td');
                imgCell.className = 'px-4 py-3';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'w-20 h-20 object-cover rounded border border-gray-300';
                img.alt = file.name;
                imgCell.appendChild(img);
                
                // File name
                const nameCell = document.createElement('td');
                nameCell.className = 'px-4 py-3 text-sm text-gray-900';
                nameCell.textContent = file.name;
                
                // Dropdown for mapping
                const selectCell = document.createElement('td');
                selectCell.className = 'px-4 py-3';
                const select = document.createElement('select');
                select.name = `mappings[${index}]`;
                select.className = 'block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500';
                select.required = true;
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select ' + (type === 'courses' ? 'Course' : 'Category') + ' --';
                select.appendChild(defaultOption);
                
                // Add items
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name + (item.code ? ' (' + item.code + ')' : '');
                    // Try to auto-match by name similarity
                    if (file.name.toLowerCase().includes(item.name.toLowerCase().substring(0, 5)) || 
                        item.name.toLowerCase().includes(file.name.toLowerCase().substring(0, 5).replace(/\.[^/.]+$/, ''))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                selectCell.appendChild(select);
                
                row.appendChild(imgCell);
                row.appendChild(nameCell);
                row.appendChild(selectCell);
                tbody.appendChild(row);
            });
        }
    </script>
</x-app-layout>

