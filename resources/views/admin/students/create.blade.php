<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Registration Form') }}
            </h2>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Students
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

            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" id="registration-form">
                @csrf

                <!-- Personal Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-green-50 border-b border-green-200">
                        <h3 class="text-lg font-semibold text-green-900">Personal Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name of the Candidate -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Name of the Candidate *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Mother's Name -->
                        <div>
                            <x-input-label for="mother_name" :value="__('Mother\'s Name')" />
                            <x-text-input id="mother_name" class="block mt-1 w-full" type="text" name="mother_name" :value="old('mother_name')" />
                            <x-input-error :messages="$errors->get('mother_name')" class="mt-2" />
                        </div>

                        <!-- Father's/Husband's Name -->
                        <div>
                            <x-input-label for="father_name" :value="__('Father\'s/Husband\'s Name')" />
                            <x-text-input id="father_name" class="block mt-1 w-full" type="text" name="father_name" :value="old('father_name')" />
                            <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <x-input-label for="date_of_birth" :value="__('Date of Birth *')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                            <p class="mt-1 text-sm text-gray-500">As per matriculation certificate</p>
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('Gender *')" />
                            <select id="gender" name="gender" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Category</option>
                                <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                                <option value="SC" {{ old('category') == 'SC' ? 'selected' : '' }}>SC</option>
                                <option value="ST" {{ old('category') == 'ST' ? 'selected' : '' }}>ST</option>
                                <option value="OBC" {{ old('category') == 'OBC' ? 'selected' : '' }}>OBC</option>
                                <option value="EWS" {{ old('category') == 'EWS' ? 'selected' : '' }}>EWS</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Aadhaar Number -->
                        <div>
                            <x-input-label for="aadhaar_number" :value="__('Aadhaar Number')" />
                            <x-text-input id="aadhaar_number" class="block mt-1 w-full" type="text" name="aadhaar_number" :value="old('aadhaar_number')" maxlength="12" />
                            <x-input-error :messages="$errors->get('aadhaar_number')" class="mt-2" />
                        </div>


                        <!-- Photo Upload -->
                        <div class="md:col-span-2">
                            <x-input-label for="photo" :value="__('Photograph *')" />
                            <input id="photo" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="photo" accept="image/*" onchange="previewPhoto(this)" required />
                            <div id="photo_preview" class="mt-4" style="display: none;">
                                <img id="photo_preview_img" src="" alt="Photo Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload passport size photograph (Max 2MB)</p>
                            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                        </div>

                        <!-- Aadhar Front Upload -->
                        <div>
                            <x-input-label for="aadhar_front" :value="__('Aadhar Front *')" />
                            <input id="aadhar_front" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="aadhar_front" accept="image/*" onchange="previewAadharFront(this)" required />
                            <div id="aadhar_front_preview" class="mt-4" style="display: none;">
                                <img id="aadhar_front_preview_img" src="" alt="Aadhar Front Preview" class="w-full h-48 object-contain rounded-lg border border-gray-300 bg-white">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload Aadhar card front side (Max 2MB)</p>
                            <x-input-error :messages="$errors->get('aadhar_front')" class="mt-2" />
                        </div>

                        <!-- Aadhar Back Upload -->
                        <div>
                            <x-input-label for="aadhar_back" :value="__('Aadhar Back *')" />
                            <input id="aadhar_back" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="aadhar_back" accept="image/*" onchange="previewAadharBack(this)" required />
                            <div id="aadhar_back_preview" class="mt-4" style="display: none;">
                                <img id="aadhar_back_preview_img" src="" alt="Aadhar Back Preview" class="w-full h-48 object-contain rounded-lg border border-gray-300 bg-white">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload Aadhar card back side (Max 2MB)</p>
                            <x-input-error :messages="$errors->get('aadhar_back')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Communication Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Communication Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="phone" :value="__('Contact Number')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>


                        <!-- Country -->
                        <div>
                            <x-input-label for="country" :value="__('Country')" />
                            <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', 'India')" />
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <!-- Nationality -->
                        <div>
                            <x-input-label for="nationality" :value="__('Nationality')" />
                            <x-text-input id="nationality" class="block mt-1 w-full" type="text" name="nationality" :value="old('nationality', 'Indian')" />
                            <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                        </div>

                        <!-- State -->
                        <div>
                            <x-input-label for="state" :value="__('State')" />
                            <select
                                id="state"
                                name="state"
                                class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select a State</option>
                            </select>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>
            
                        <!-- District -->
                        <div>
                            <x-input-label for="district" :value="__('District')" />
                            <x-text-input id="district" class="block mt-1 w-full" type="text" name="district" :value="old('district')" placeholder="Enter district name" />
                            <x-input-error :messages="$errors->get('district')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="3" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Pin Code -->
                        <div>
                            <x-input-label for="pin_code" :value="__('Pin Code')" />
                            <x-text-input id="pin_code" class="block mt-1 w-full" type="text" name="pin_code" :value="old('pin_code')" maxlength="6" />
                            <x-input-error :messages="$errors->get('pin_code')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Previous Qualification Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-red-50 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-900text-red-100">Previous Qualification Details</h3>
                    </div>
                    <div class="p-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Examination</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Of Passing</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Board/University</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage (%)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $examinations = [
                                        'secondary' => 'Secondary',
                                        'sr_secondary' => 'Sr. Secondary',
                                        'graduation' => 'Graduation',
                                        'post_graduation' => 'Post Graduation',
                                        'other' => 'Others'
                                    ];
                                @endphp
                                @foreach($examinations as $key => $label)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $label }}
                                            <input type="hidden" name="qualifications[{{ $loop->index }}][examination]" value="{{ $key }}">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text" name="qualifications[{{ $loop->index }}][year_of_passing]" value="{{ old('qualifications.'.$loop->index.'.year_of_passing', 'yyyy') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="yyyy">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text" name="qualifications[{{ $loop->index }}][board_university]" value="{{ old('qualifications.'.$loop->index.'.board_university') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number" step="0.01" name="qualifications[{{ $loop->index }}][percentage]" value="{{ old('qualifications.'.$loop->index.'.percentage') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="%" min="0" max="100">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Academic Certificate Uploads -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Upload Academic Certificates</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Class 10th Certificate -->
                            <div>
                                <x-input-label for="certificate_class_10th" :value="__('Class 10th Certificate')" />
                                <input id="certificate_class_10th" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="certificate_class_10th" accept="image/*,.pdf" onchange="previewCertificate(this, 'class_10th')" />
                                <div id="certificate_class_10th_preview" class="mt-4" style="display: none;">
                                    <div id="certificate_class_10th_preview_content" class="border border-gray-300 rounded-lg p-2 bg-gray-50"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Upload Class 10th mark sheet/certificate (Image or PDF, Max 5MB)</p>
                                <x-input-error :messages="$errors->get('certificate_class_10th')" class="mt-2" />
                            </div>

                            <!-- Class 12th Certificate -->
                            <div>
                                <x-input-label for="certificate_class_12th" :value="__('Class 12th Certificate')" />
                                <input id="certificate_class_12th" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="certificate_class_12th" accept="image/*,.pdf" onchange="previewCertificate(this, 'class_12th')" />
                                <div id="certificate_class_12th_preview" class="mt-4" style="display: none;">
                                    <div id="certificate_class_12th_preview_content" class="border border-gray-300 rounded-lg p-2 bg-gray-50"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Upload Class 12th mark sheet/certificate (Image or PDF, Max 5MB)</p>
                                <x-input-error :messages="$errors->get('certificate_class_12th')" class="mt-2" />
                            </div>

                            <!-- Graduation Certificate -->
                            <div>
                                <x-input-label for="certificate_graduation" :value="__('Graduation Certificate')" />
                                <input id="certificate_graduation" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="certificate_graduation" accept="image/*,.pdf" onchange="previewCertificate(this, 'graduation')" />
                                <div id="certificate_graduation_preview" class="mt-4" style="display: none;">
                                    <div id="certificate_graduation_preview_content" class="border border-gray-300 rounded-lg p-2 bg-gray-50"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Upload Graduation degree/certificate (Image or PDF, Max 5MB)</p>
                                <x-input-error :messages="$errors->get('certificate_graduation')" class="mt-2" />
                            </div>

                            <!-- Other Certificates -->
                            <div>
                                <x-input-label for="certificate_others" :value="__('Other Certificates')" />
                                <input id="certificate_others" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="certificate_others" accept="image/*,.pdf" onchange="previewCertificate(this, 'others')" />
                                <div id="certificate_others_preview" class="mt-4" style="display: none;">
                                    <div id="certificate_others_preview_content" class="border border-gray-300 rounded-lg p-2 bg-gray-50"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Upload any other certificates (Image or PDF, Max 5MB)</p>
                                <x-input-error :messages="$errors->get('certificate_others')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programme Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Programme Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(isset($institutes))
                        <!-- Institute Selection (for all admins) -->
                        <div class="md:col-span-2">
                            <x-input-label for="institute_id" :value="__('Institute *')" />
                            <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="loadCategories(this.value)">
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ (old('institute_id', isset($currentInstituteId) ? $currentInstituteId : '')) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Select an institute to view available categories and courses</p>
                        </div>
                        @endif
                        
                        <!-- Course Category -->
                        <div class="md:col-span-2">
                            <x-input-label for="category_filter" :value="__('Course Category')" />
                            <select id="category_filter" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" onchange="filterCoursesByCategory(this.value)">
                                <option value="">All Categories (Select to filter courses)</option>
                                @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-institute-id="{{ $category->institute_id }}">{{ $category->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Filter courses by category (optional)</p>
                        </div>

                        <!-- Course -->
                        <div class="md:col-span-2">
                            <x-input-label for="course_id" :value="__('Course *')" />
                            <select id="course_id" name="course_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="loadCourseFees(this.value); updateSession();">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-institute-id="{{ $course->institute_id }}" data-category-id="{{ $course->category_id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}@if($course->category) [{{ $course->category->name }}]@endif</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <!-- Session -->
                        <div>
                            <x-input-label for="session" :value="__('Session')" />
                            <select id="session" name="session" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select</option>
                                @php
                                    $currentYear = date('Y');
                                    $defaultSession = $currentYear . '-' . ($currentYear + 1);
                                @endphp
                                @for($year = date('Y'); $year <= date('Y') + 2; $year++)
                                    <option value="{{ $year }}-{{ $year + 1 }}" {{ old('session', $defaultSession) == ($year . '-' . ($year + 1)) ? 'selected' : '' }}>{{ $year }}-{{ $year + 1 }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('session')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Course Fee Information (Read-only) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Course Fee Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Total Course Fee (from course) -->
                            <div>
                                <x-input-label for="course_total_fee" :value="__('Total Course Fee')" />
                                <x-text-input id="course_total_fee" class="block mt-1 w-full bg-gray-100 font-semibold text-lg" type="text" readonly placeholder="Select a course" />
                                <p class="mt-1 text-xs text-gray-500">Auto-filled from selected course</p>
                            </div>
                            
                            <!-- Fee Per Year -->
                            <div>
                                <x-input-label for="course_fee_per_year" :value="__('Fee Per Year')" />
                                <x-text-input id="course_fee_per_year" class="block mt-1 w-full bg-gray-100" type="text" readonly placeholder="—" />
                            </div>
                            
                            <!-- Course Duration -->
                            <div>
                                <x-input-label for="course_duration" :value="__('Course Duration')" />
                                <x-text-input id="course_duration" class="block mt-1 w-full bg-gray-100" type="text" readonly placeholder="—" />
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-blue-800">
                                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong>Note:</strong> Fee payments can be added after student registration from the student's profile page.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Login Credentials</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password *')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password *')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Declaration Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Declaration by the Applicant</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 text-sm text-gray-700 mb-6">
                            <p>
                                I hereby declare that entries made by me in this admission form and the documents submitted by me along with it, are true to the best of my knowledge, in all respects and in any case, if any information is found to be false, this shall entail automatic cancellation of my admission and forfeiture of all fee deposited, besides rendering me liable to such action as the University may deem proper.
                            </p>
                            <p>
                                I take note that my admission to the University and continuation on its roll are subject to the provisions of rules of the University, issued from time to time. I shall abide by the rules of discipline and proper conduct. I am fully aware of the law regarding ragging as well as the punishment and that if, found guilty on this account I am liable to be punished appropriately. I hereby undertake that I shall not indulge in any act of ragging.
                            </p>
                        </div>
                        <div class="flex items-start">
                            <input type="checkbox" id="declaration_accepted" name="declaration_accepted" value="1" {{ old('declaration_accepted') ? 'checked' : '' }} required class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="declaration_accepted" class="ml-2 text-sm text-gray-700">
                                I accept the above declaration and agree to abide by all the rules and regulations of the institute. *
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('declaration_accepted')" class="mt-2" />
                        
                        <!-- Signature Upload (Consent) -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <x-input-label for="signature" :value="__('Signature (Consent) *')" />
                            <p class="text-sm text-gray-600 mb-3">Please upload your signature as a confirmation of your consent to the above declaration.</p>
                            <input id="signature" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="signature" accept="image/*" onchange="previewSignature(this)" required />
                            <div id="signature_preview" class="mt-4" style="display: none;">
                                <img id="signature_preview_img" src="" alt="Signature Preview" class="w-48 h-24 object-contain rounded-lg border border-gray-300 bg-white">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload scanned signature (Max 2MB). This signature will be used on your registration form as proof of consent.</p>
                            <x-input-error :messages="$errors->get('signature')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 flex justify-end space-x-4">
                        <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            Submit Application
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>

        function previewPhoto(input) {
            const preview = document.getElementById('photo_preview');
            const previewImg = document.getElementById('photo_preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function previewSignature(input) {
            const preview = document.getElementById('signature_preview');
            const previewImg = document.getElementById('signature_preview_img');
            
            if (!preview || !previewImg) {
                console.error('Signature preview elements not found');
                return;
            }
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.onerror = function(error) {
                    console.error('Error reading signature file:', error);
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function previewAadharFront(input) {
            const preview = document.getElementById('aadhar_front_preview');
            const previewImg = document.getElementById('aadhar_front_preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function previewAadharBack(input) {
            const preview = document.getElementById('aadhar_back_preview');
            const previewImg = document.getElementById('aadhar_back_preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function previewCertificate(input, type) {
            const preview = document.getElementById('certificate_' + type + '_preview');
            const previewContent = document.getElementById('certificate_' + type + '_preview_content');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                
                if (fileType.startsWith('image/')) {
                    // Image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContent.innerHTML = '<img src="' + e.target.result + '" alt="Certificate Preview" class="w-full h-48 object-contain rounded border border-gray-300 bg-white">';
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === 'application/pdf') {
                    // PDF preview - show file name and icon
                    previewContent.innerHTML = '<div class="flex items-center p-4 bg-white rounded border border-gray-300"><svg class="w-12 h-12 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg><div><p class="text-sm font-medium text-gray-900">' + file.name + '</p><p class="text-xs text-gray-500">PDF Document</p></div></div>';
                    preview.style.display = 'block';
                } else {
                    previewContent.innerHTML = '<div class="p-4 bg-white rounded border border-gray-300 text-sm text-gray-600">File: ' + file.name + '</div>';
                    preview.style.display = 'block';
                }
            } else {
                preview.style.display = 'none';
            }
        }


        // Initialize employment fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize session and admission year
            updateSession();
            
            // Filter categories and courses by institute (for all admins)
            @if(isset($institutes))
            const instituteSelect = document.getElementById('institute_id');
            if (instituteSelect) {
                // Filter categories and courses based on currently selected institute
                // If an institute is pre-selected from session, filter by that
                if (instituteSelect.value) {
                    loadCategories(instituteSelect.value);
                } else {
                    // Show all options if no institute is selected
                    const categorySelect = document.getElementById('category_filter');
                    const courseSelect = document.getElementById('course_id');
                    if (categorySelect) {
                        const allCategories = categorySelect.querySelectorAll('option');
                        allCategories.forEach(option => {
                            option.style.display = '';
                        });
                    }
                    if (courseSelect) {
                        const allCourses = courseSelect.querySelectorAll('option');
                        allCourses.forEach(option => {
                            option.style.display = '';
                        });
                    }
                }
            }
            @endif
            
            // Load fees if course is already selected (from old input)
            const courseSelect = document.getElementById('course_id');
            if (courseSelect && courseSelect.value) {
                loadCourseFees(courseSelect.value);
            }

            // Initialize Indian states and districts
            initStatesAndDistricts();
        });

        // Courses data from server
        const coursesData = @json(isset($coursesJson) ? json_decode($coursesJson, true) : []);
        
        // Categories data from server
        const categoriesData = @json(isset($categoriesJson) ? json_decode($categoriesJson, true) : []);

        // Indian states and union territories with representative districts (can be extended later)
        const indianLocations = {
            "Andhra Pradesh": ["Anantapur", "Chittoor", "East Godavari", "Guntur", "Krishna", "Kurnool", "Nellore", "Prakasam", "Srikakulam", "Visakhapatnam", "Vizianagaram", "West Godavari", "YSR Kadapa"],
            "Arunachal Pradesh": ["Tawang", "West Kameng", "East Kameng", "Papum Pare", "Upper Subansiri", "West Siang"],
            "Assam": ["Barpeta", "Bongaigaon", "Cachar", "Darrang", "Dibrugarh", "Goalpara", "Golaghat", "Jorhat", "Kamrup", "Karimganj", "Nagaon", "Sivasagar", "Sonitpur", "Tinsukia"],
            "Bihar": ["Patna", "Gaya", "Bhagalpur", "Muzaffarpur", "Nalanda", "Purnia", "Darbhanga", "Aurangabad"],
            "Chhattisgarh": ["Balod", "Bilaspur", "Durg", "Janjgir-Champa", "Korba", "Raipur", "Rajnandgaon"],
            "Goa": ["North Goa", "South Goa"],
            "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Junagadh", "Kheda"],
            "Haryana": ["Ambala", "Faridabad", "Gurugram", "Hisar", "Karnal", "Kurukshetra", "Panipat", "Rohtak"],
            "Himachal Pradesh": ["Bilaspur", "Chamba", "Hamirpur", "Kangra", "Kinnaur", "Kullu", "Mandi", "Shimla", "Solan", "Una"],
            "Jharkhand": ["Bokaro", "Deoghar", "Dhanbad", "Giridih", "Hazaribagh", "Jamshedpur", "Ranchi"],
            "Karnataka": ["Bagalkot", "Ballari", "Belagavi", "Bengaluru Rural", "Bengaluru Urban", "Bidar", "Dakshina Kannada", "Dharwad", "Gulbarga", "Hassan", "Mysuru", "Shivamogga", "Udupi"],
            "Kerala": ["Alappuzha", "Ernakulam", "Idukki", "Kannur", "Kasaragod", "Kollam", "Kottayam", "Kozhikode", "Malappuram", "Palakkad", "Pathanamthitta", "Thiruvananthapuram", "Thrissur", "Wayanad"],
            "Madhya Pradesh": ["Bhopal", "Indore", "Gwalior", "Jabalpur", "Ujjain", "Rewa", "Sagar", "Satna"],
            "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Nashik", "Thane", "Aurangabad", "Kolhapur", "Solapur", "Satara"],
            "Manipur": ["Bishnupur", "Chandel", "Imphal East", "Imphal West", "Thoubal", "Senapati", "Ukhrul"],
            "Meghalaya": ["East Garo Hills", "West Garo Hills", "East Khasi Hills", "West Khasi Hills", "Jaintia Hills"],
            "Mizoram": ["Aizawl", "Champhai", "Kolasib", "Lunglei", "Mamit", "Saiha", "Serchhip"],
            "Nagaland": ["Dimapur", "Kohima", "Mokokchung", "Mon", "Phek", "Tuensang", "Wokha", "Zunheboto"],
            "Odisha": ["Angul", "Balasore", "Balangir", "Bhadrak", "Cuttack", "Ganjam", "Jajpur", "Khurda", "Mayurbhanj", "Puri", "Sambalpur", "Sundargarh"],
            "Punjab": ["Amritsar", "Barnala", "Bathinda", "Faridkot", "Ferozepur", "Gurdaspur", "Hoshiarpur", "Jalandhar", "Ludhiana", "Moga", "Patiala", "Sangrur"],
            "Rajasthan": ["Ajmer", "Alwar", "Bharatpur", "Bhilwara", "Bikaner", "Chittorgarh", "Jaipur", "Jodhpur", "Kota", "Udaipur"],
            "Sikkim": ["East Sikkim", "North Sikkim", "South Sikkim", "West Sikkim"],
            "Tamil Nadu": ["Chennai", "Coimbatore", "Cuddalore", "Dharmapuri", "Dindigul", "Erode", "Kanchipuram", "Madurai", "Salem", "Thanjavur", "Thoothukudi", "Tiruchirappalli", "Tirunelveli", "Vellore"],
            "Telangana": ["Adilabad", "Hyderabad", "Karimnagar", "Khammam", "Mahbubnagar", "Medak", "Nalgonda", "Nizamabad", "Ranga Reddy", "Warangal"],
            "Tripura": ["Dhalai", "Gomati", "Khowai", "North Tripura", "Sepahijala", "South Tripura", "Unakoti", "West Tripura"],
            "Uttar Pradesh": ["Agra", "Aligarh", "Allahabad", "Bareilly", "Ghaziabad", "Gorakhpur", "Jhansi", "Kanpur Nagar", "Lucknow", "Meerut", "Moradabad", "Noida", "Varanasi"],
            "Uttarakhand": ["Almora", "Dehradun", "Haridwar", "Nainital", "Pauri Garhwal", "Pithoragarh", "Rudraprayag", "Tehri Garhwal", "Udham Singh Nagar"],
            "West Bengal": ["Alipurduar", "Bankura", "Birbhum", "Cooch Behar", "Darjeeling", "Hooghly", "Howrah", "Jalpaiguri", "Kolkata", "Malda", "Murshidabad", "Nadia", "North 24 Parganas", "South 24 Parganas"],
            // Union Territories
            "Andaman and Nicobar Islands": ["Nicobar", "North and Middle Andaman", "South Andaman"],
            "Chandigarh": ["Chandigarh"],
            "Dadra and Nagar Haveli and Daman and Diu": ["Dadra and Nagar Haveli", "Daman", "Diu"],
            "Delhi": ["Central Delhi", "East Delhi", "New Delhi", "North Delhi", "North East Delhi", "North West Delhi", "South Delhi", "South East Delhi", "South West Delhi", "West Delhi"],
            "Jammu and Kashmir": ["Anantnag", "Baramulla", "Budgam", "Jammu", "Kathua", "Kupwara", "Pulwama", "Srinagar", "Udhampur"],
            "Ladakh": ["Kargil", "Leh"],
            "Lakshadweep": ["Lakshadweep"],
            "Puducherry": ["Karaikal", "Mahe", "Puducherry", "Yanam"]
        };
        
        // Load categories and courses based on selected institute
        function loadCategories(instituteId) {
            const categorySelect = document.getElementById('category_filter');
            const courseSelect = document.getElementById('course_id');
            const allCategories = categorySelect.querySelectorAll('option');
            const allCourses = courseSelect.querySelectorAll('option');
            
            // Filter categories by institute
            allCategories.forEach(option => {
                option.style.display = '';
                if (option.value && instituteId) {
                    const optionInstituteId = String(option.dataset.instituteId || '');
                    if (optionInstituteId !== String(instituteId)) {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Reset category selection
            categorySelect.value = '';
            
            // Filter courses by institute - convert both to strings for comparison
            allCourses.forEach(option => {
                option.style.display = '';
            });
            
            if (instituteId) {
                const instituteIdStr = String(instituteId);
                allCourses.forEach(option => {
                    if (option.value) {
                        const optionInstituteId = String(option.dataset.instituteId || '');
                        if (optionInstituteId !== instituteIdStr) {
                            option.style.display = 'none';
                        }
                    }
                });
                // Reset selection if current selection is hidden
                if (courseSelect.value) {
                    const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                    if (selectedOption && selectedOption.style.display === 'none') {
                        courseSelect.value = '';
                        // Clear fees when course is cleared
                        clearFees();
                    }
                }
            }
        }
        
        // Filter courses by category
        function filterCoursesByCategory(categoryId) {
            const instituteSelect = document.getElementById('institute_id');
            const courseSelect = document.getElementById('course_id');
            const allCourses = courseSelect.querySelectorAll('option');
            const instituteId = instituteSelect ? instituteSelect.value : '';
            
            // First, show all courses for the selected institute
            allCourses.forEach(option => {
                option.style.display = '';
                if (option.value && instituteId) {
                    const optionInstituteId = String(option.dataset.instituteId || '');
                    if (optionInstituteId !== String(instituteId)) {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Then, if a category is selected, further filter by category
            if (categoryId) {
                const categoryIdStr = String(categoryId);
                allCourses.forEach(option => {
                    if (option.value && option.style.display !== 'none') {
                        const optionCategoryId = String(option.dataset.categoryId || '');
                        // Hide if category doesn't match (but allow courses with no category when showing all)
                        if (optionCategoryId !== categoryIdStr) {
                            option.style.display = 'none';
                        }
                    }
                });
            }
            
            // Reset course selection if current selection is hidden
            if (courseSelect.value) {
                const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                if (selectedOption && selectedOption.style.display === 'none') {
                    courseSelect.value = '';
                    clearFees();
                }
            }
        }
        
        // Legacy function for backward compatibility
        function loadCourses(instituteId) {
            loadCategories(instituteId);
        }
        
        // Load course fee info when course is selected (read-only display)
        function loadCourseFees(courseId) {
            if (!courseId) {
                clearFees();
                return;
            }
            
            // Find course in coursesData
            const course = coursesData.find(c => String(c.id) === String(courseId));
            
            if (course) {
                // Display course fee info (read-only)
                const totalFee = parseFloat(course.tuition_fee) || 0;
                const totalMonths = parseInt(course.duration_months) || 0;
                
                // Calculate total duration in years for fee calculation
                const totalYears = totalMonths / 12;
                const feePerYear = totalYears > 0 ? (totalFee / totalYears) : totalFee;
                
                // Format duration display
                let durationText = 'Not specified';
                if (totalMonths > 0) {
                    if (totalMonths < 12) {
                        durationText = totalMonths + ' month' + (totalMonths > 1 ? 's' : '');
                    } else {
                        const years = Math.floor(totalMonths / 12);
                        const months = totalMonths % 12;
                        if (months > 0) {
                            durationText = years + ' year' + (years > 1 ? 's' : '') + ' ' + months + ' month' + (months > 1 ? 's' : '');
                        } else {
                            durationText = years + ' year' + (years > 1 ? 's' : '');
                        }
                    }
                }
                
                document.getElementById('course_total_fee').value = '₹ ' + totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2});
                document.getElementById('course_fee_per_year').value = '₹ ' + feePerYear.toLocaleString('en-IN', {minimumFractionDigits: 2});
                document.getElementById('course_duration').value = durationText;
            } else {
                clearFees();
            }
        }

        
        // Clear course fee display
        function clearFees() {
            document.getElementById('course_total_fee').value = '';
            document.getElementById('course_total_fee').placeholder = 'Select a course';
            document.getElementById('course_fee_per_year').value = '';
            document.getElementById('course_fee_per_year').placeholder = '—';
            document.getElementById('course_duration').value = '';
            document.getElementById('course_duration').placeholder = '—';
        }
        
        // Auto-populate session based on current year
        function updateSession() {
            const currentYear = new Date().getFullYear();
            const sessionSelect = document.getElementById('session');
            const admissionYearInput = document.getElementById('admission_year');
            
            if (sessionSelect && !sessionSelect.value) {
                // Set default session to current year - next year
                const defaultSession = `${currentYear}-${currentYear + 1}`;
                sessionSelect.value = defaultSession;
            }
            
            if (admissionYearInput && !admissionYearInput.value) {
                admissionYearInput.value = currentYear;
            }
        }

        // Populate states dropdown and pre-select old value if present
        function initStatesAndDistricts() {
            const stateSelect = document.getElementById('state');
            if (!stateSelect) return;

            const oldState = @json(old('state'));

            // Populate states
            Object.keys(indianLocations).sort().forEach(stateName => {
                const option = document.createElement('option');
                option.value = stateName;
                option.textContent = stateName;
                if (oldState && oldState === stateName) {
                    option.selected = true;
                }
                stateSelect.appendChild(option);
            });
        }

    </script>
</x-app-layout>

