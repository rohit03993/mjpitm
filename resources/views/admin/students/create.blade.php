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

                        <!-- Passport Number -->
                        <div>
                            <x-input-label for="passport_number" :value="__('Passport Number')" />
                            <x-text-input id="passport_number" class="block mt-1 w-full" type="text" name="passport_number" :value="old('passport_number')" />
                            <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
                        </div>

                        <!-- Are you employed? -->
                        <div>
                            <x-input-label for="is_employed" :value="__('Are you employed?')" />
                            <select id="is_employed" name="is_employed" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" onchange="toggleEmploymentFields(this.value)">
                                <option value="0" {{ old('is_employed') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_employed') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            <x-input-error :messages="$errors->get('is_employed')" class="mt-2" />
                        </div>

                        <!-- Employer Name -->
                        <div id="employer_name_field" style="display: none;">
                            <x-input-label for="employer_name" :value="__('Employer Name')" />
                            <x-text-input id="employer_name" class="block mt-1 w-full" type="text" name="employer_name" :value="old('employer_name')" />
                            <x-input-error :messages="$errors->get('employer_name')" class="mt-2" />
                        </div>

                        <!-- Designation -->
                        <div id="designation_field" style="display: none;">
                            <x-input-label for="designation" :value="__('Designation')" />
                            <x-text-input id="designation" class="block mt-1 w-full" type="text" name="designation" :value="old('designation')" />
                            <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                        </div>

                        <!-- Photo Upload -->
                        <div class="md:col-span-2">
                            <x-input-label for="photo" :value="__('Photo')" />
                            <input id="photo" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="photo" accept="image/*" onchange="previewPhoto(this)" />
                            <div id="photo_preview" class="mt-4" style="display: none;">
                                <img id="photo_preview_img" src="" alt="Photo Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            </div>
                            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Communication Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-900">Communication Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Number -->
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

                        <!-- Father's Contact No -->
                        <div>
                            <x-input-label for="father_contact" :value="__('Father\'s Contact No')" />
                            <x-text-input id="father_contact" class="block mt-1 w-full" type="tel" name="father_contact" :value="old('father_contact')" />
                            <x-input-error :messages="$errors->get('father_contact')" class="mt-2" />
                        </div>

                        <!-- Mother's Contact No -->
                        <div>
                            <x-input-label for="mother_contact" :value="__('Mother\'s Contact No')" />
                            <x-text-input id="mother_contact" class="block mt-1 w-full" type="tel" name="mother_contact" :value="old('mother_contact')" />
                            <x-input-error :messages="$errors->get('mother_contact')" class="mt-2" />
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
                            <x-text-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" placeholder="Select a State" />
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <!-- District -->
                        <div>
                            <x-input-label for="district" :value="__('District')" />
                            <x-text-input id="district" class="block mt-1 w-full" type="text" name="district" :value="old('district')" />
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage/CGPA</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
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
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="number" step="0.01" name="qualifications[{{ $loop->index }}][percentage]" value="{{ old('qualifications.'.$loop->index.'.percentage') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="%" min="0" max="100">
                                                <input type="text" name="qualifications[{{ $loop->index }}][cgpa]" value="{{ old('qualifications.'.$loop->index.'.cgpa') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="CGPA">
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="text" name="qualifications[{{ $loop->index }}][subjects]" value="{{ old('qualifications.'.$loop->index.'.subjects') }}" class="block w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                            <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="loadCourses(this.value)">
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}" {{ (old('institute_id', isset($currentInstituteId) ? $currentInstituteId : '')) == $institute->id ? 'selected' : '' }}>{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Select an institute to view available courses</p>
                        </div>
                        @endif
                        
                        <!-- Course -->
                        <div class="md:col-span-2">
                            <x-input-label for="course_id" :value="__('Course *')" />
                            <select id="course_id" name="course_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required onchange="loadCourseFees(this.value); updateSession();">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-institute-id="{{ $course->institute_id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}@if(isset($institutes)) ({{ $course->institute->name ?? '' }})@endif</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <!-- Stream -->
                        <div>
                            <x-input-label for="stream" :value="__('Stream')" />
                            <x-text-input id="stream" class="block mt-1 w-full" type="text" name="stream" :value="old('stream')" />
                            <x-input-error :messages="$errors->get('stream')" class="mt-2" />
                        </div>

                        <!-- Year -->
                        <div>
                            <x-input-label for="admission_year" :value="__('Admission Year *')" />
                            <x-text-input id="admission_year" class="block mt-1 w-full" type="text" name="admission_year" :value="old('admission_year', date('Y'))" required />
                            <x-input-error :messages="$errors->get('admission_year')" class="mt-2" />
                        </div>

                        <!-- Session -->
                        <div>
                            <x-input-label for="session" :value="__('Session')" />
                            <select id="session" name="session" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select</option>
                                @for($year = date('Y'); $year <= date('Y') + 2; $year++)
                                    <option value="{{ $year }}-{{ $year + 1 }}" {{ old('session') == ($year . '-' . ($year + 1)) ? 'selected' : '' }}>{{ $year }}-{{ $year + 1 }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('session')" class="mt-2" />
                        </div>

                        <!-- Mode of Study -->
                        <div>
                            <x-input-label for="mode_of_study" :value="__('Mode of Study *')" />
                            <select id="mode_of_study" name="mode_of_study" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="regular" {{ old('mode_of_study') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="distance" {{ old('mode_of_study') == 'distance' ? 'selected' : '' }}>Distance</option>
                            </select>
                            <x-input-error :messages="$errors->get('mode_of_study')" class="mt-2" />
                        </div>

                        <!-- Admission Type -->
                        <div>
                            <x-input-label for="admission_type" :value="__('Admission Type *')" />
                            <select id="admission_type" name="admission_type" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Normal" {{ old('admission_type') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Lateral" {{ old('admission_type') == 'Lateral' ? 'selected' : '' }}>Lateral</option>
                                <option value="Direct" {{ old('admission_type') == 'Direct' ? 'selected' : '' }}>Direct</option>
                            </select>
                            <x-input-error :messages="$errors->get('admission_type')" class="mt-2" />
                        </div>

                        <!-- Hostel Facility -->
                        <div>
                            <x-input-label for="hostel_facility_required" :value="__('Hostel Facility')" />
                            <select id="hostel_facility" name="hostel_facility" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="0" {{ old('hostel_facility') == '0' ? 'selected' : '' }}>No Facility Required</option>
                                <option value="1" {{ old('hostel_facility') == '1' ? 'selected' : '' }}>Hostel Required</option>
                            </select>
                            <x-input-error :messages="$errors->get('hostel_facility_required')" class="mt-2" />
                        </div>

                        <!-- Current Semester -->
                        <div>
                            <x-input-label for="current_semester" :value="__('Current Semester')" />
                            <x-text-input id="current_semester" class="block mt-1 w-full" type="number" name="current_semester" :value="old('current_semester', 1)" min="1" />
                            <x-input-error :messages="$errors->get('current_semester')" class="mt-2" />
                        </div>

                        <!-- Roll Number -->
                        <div>
                            <x-input-label for="roll_number" :value="__('Roll Number *')" />
                            <x-text-input id="roll_number" class="block mt-1 w-full" type="text" name="roll_number" :value="old('roll_number')" required />
                            <x-input-error :messages="$errors->get('roll_number')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Fee Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-yellow-50 border-b border-yellow-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-yellow-900">Fee Details</h3>
                        <label class="flex items-center">
                            <input type="checkbox" name="pay_in_installment" value="1" {{ old('pay_in_installment') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-yellow-900">Pay In Installment</span>
                        </label>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Registration Fee -->
                        <div>
                            <x-input-label for="registration_fee" :value="__('Registration Fee')" />
                            <x-text-input id="registration_fee" class="block mt-1 w-full" type="number" step="0.01" name="registration_fee" :value="old('registration_fee')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('registration_fee')" class="mt-2" />
                        </div>

                        <!-- Entrance Fee -->
                        <div>
                            <x-input-label for="entrance_fee" :value="__('Entrance Fee')" />
                            <x-text-input id="entrance_fee" class="block mt-1 w-full" type="number" step="0.01" name="entrance_fee" :value="old('entrance_fee')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('entrance_fee')" class="mt-2" />
                        </div>

                        <!-- Enrollment Fee -->
                        <div>
                            <x-input-label for="enrollment_fee" :value="__('Enrollment Fee')" />
                            <x-text-input id="enrollment_fee" class="block mt-1 w-full" type="number" step="0.01" name="enrollment_fee" :value="old('enrollment_fee')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('enrollment_fee')" class="mt-2" />
                        </div>

                        <!-- Tuition Fee -->
                        <div>
                            <x-input-label for="tuition_fee" :value="__('Tuition Fee')" />
                            <x-text-input id="tuition_fee" class="block mt-1 w-full" type="number" step="0.01" name="tuition_fee" :value="old('tuition_fee')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('tuition_fee')" class="mt-2" />
                        </div>

                        <!-- Caution Money -->
                        <div>
                            <x-input-label for="caution_money" :value="__('Caution Money')" />
                            <x-text-input id="caution_money" class="block mt-1 w-full" type="number" step="0.01" name="caution_money" :value="old('caution_money')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('caution_money')" class="mt-2" />
                        </div>

                        <!-- Hostel Fee -->
                        <div>
                            <x-input-label for="hostel_fee_amount" :value="__('Hostel Fee')" />
                            <x-text-input id="hostel_fee_amount" class="block mt-1 w-full" type="number" step="0.01" name="hostel_fee_amount" :value="old('hostel_fee_amount')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('hostel_fee_amount')" class="mt-2" />
                        </div>

                        <!-- Late Fee -->
                        <div>
                            <x-input-label for="late_fee" :value="__('Late Fee')" />
                            <x-text-input id="late_fee" class="block mt-1 w-full" type="number" step="0.01" name="late_fee" :value="old('late_fee')" min="0" oninput="calculateTotal()" />
                            <x-input-error :messages="$errors->get('late_fee')" class="mt-2" />
                        </div>

                        <!-- Total Deposit -->
                        <div>
                            <x-input-label for="total_deposit" :value="__('Total Deposit')" />
                            <x-text-input id="total_deposit" class="block mt-1 w-full bg-gray-100" type="number" step="0.01" name="total_deposit" :value="old('total_deposit')" min="0" readonly />
                            <x-input-error :messages="$errors->get('total_deposit')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Payment Details Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-green-50 border-b border-green-200">
                        <h3 class="text-lg font-semibold text-green-900text-green-100">Payment Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mode of Payment -->
                        <div>
                            <x-input-label for="payment_mode" :value="__('Mode of Payment')" />
                            <select id="payment_mode" name="payment_mode" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select</option>
                                <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Cheque" {{ old('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Online" {{ old('payment_mode') == 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="DD" {{ old('payment_mode') == 'DD' ? 'selected' : '' }}>Demand Draft (DD)</option>
                                <option value="Bank Transfer" {{ old('payment_mode') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_mode')" class="mt-2" />
                        </div>

                        <!-- Bank Account -->
                        <div>
                            <x-input-label for="bank_account" :value="__('Bank Account')" />
                            <select id="bank_account" name="bank_account" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select</option>
                                <option value="Bank Account 1" {{ old('bank_account') == 'Bank Account 1' ? 'selected' : '' }}>Bank Account 1</option>
                                <option value="Bank Account 2" {{ old('bank_account') == 'Bank Account 2' ? 'selected' : '' }}>Bank Account 2</option>
                            </select>
                            <x-input-error :messages="$errors->get('bank_account')" class="mt-2" />
                        </div>

                        <!-- Deposit Date -->
                        <div>
                            <x-input-label for="deposit_date" :value="__('Deposit Date')" />
                            <x-text-input id="deposit_date" class="block mt-1 w-full" type="date" name="deposit_date" :value="old('deposit_date')" />
                            <x-input-error :messages="$errors->get('deposit_date')" class="mt-2" />
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
        function toggleEmploymentFields(value) {
            const employerNameField = document.getElementById('employer_name_field');
            const designationField = document.getElementById('designation_field');
            
            if (value == '1') {
                employerNameField.style.display = 'block';
                designationField.style.display = 'block';
            } else {
                employerNameField.style.display = 'none';
                designationField.style.display = 'none';
                document.getElementById('employer_name').value = '';
                document.getElementById('designation').value = '';
            }
        }

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

        function calculateTotal() {
            const registrationFee = parseFloat(document.getElementById('registration_fee').value) || 0;
            const entranceFee = parseFloat(document.getElementById('entrance_fee').value) || 0;
            const enrollmentFee = parseFloat(document.getElementById('enrollment_fee').value) || 0;
            const tuitionFee = parseFloat(document.getElementById('tuition_fee').value) || 0;
            const cautionMoney = parseFloat(document.getElementById('caution_money').value) || 0;
            const hostelFee = parseFloat(document.getElementById('hostel_fee_amount').value) || 0;
            const lateFee = parseFloat(document.getElementById('late_fee').value) || 0;
            
            const total = registrationFee + entranceFee + enrollmentFee + tuitionFee + cautionMoney + hostelFee + lateFee;
            document.getElementById('total_deposit').value = total.toFixed(2);
        }

        // Initialize employment fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isEmployedSelect = document.getElementById('is_employed');
            if (isEmployedSelect) {
                toggleEmploymentFields(isEmployedSelect.value);
            }
            
            // Initialize session and admission year
            updateSession();
            
            // Filter courses by institute (for all admins)
            @if(isset($institutes))
            const instituteSelect = document.getElementById('institute_id');
            if (instituteSelect) {
                // Filter courses based on currently selected institute
                // If an institute is pre-selected from session, filter by that
                // Otherwise, show all courses initially
                if (instituteSelect.value) {
                    loadCourses(instituteSelect.value);
                } else {
                    // Show all courses if no institute is selected
                    const courseSelect = document.getElementById('course_id');
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
        });

        // Courses data from server
        const coursesData = @json(isset($coursesJson) ? json_decode($coursesJson, true) : []);
        
        // Load courses based on selected institute (for Super Admin)
        function loadCourses(instituteId) {
            const courseSelect = document.getElementById('course_id');
            const allCourses = courseSelect.querySelectorAll('option');
            
            // Show all options first
            allCourses.forEach(option => {
                option.style.display = '';
            });
            
            // Filter courses by institute - convert both to strings for comparison
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
            } else {
                // If no institute selected, show all courses
                allCourses.forEach(option => {
                    option.style.display = '';
                });
            }
        }
        
        // Load fees when course is selected
        function loadCourseFees(courseId) {
            if (!courseId) {
                clearFees();
                return;
            }
            
            // Find course in coursesData
            const course = coursesData.find(c => String(c.id) === String(courseId));
            
            if (course) {
                // Populate fee fields
                document.getElementById('registration_fee').value = course.registration_fee || 0;
                document.getElementById('entrance_fee').value = course.entrance_fee || 0;
                document.getElementById('enrollment_fee').value = course.enrollment_fee || 0;
                document.getElementById('tuition_fee').value = course.tuition_fee || 0;
                document.getElementById('caution_money').value = course.caution_money || 0;
                document.getElementById('hostel_fee_amount').value = course.hostel_fee_amount || 0;
                document.getElementById('late_fee').value = course.late_fee || 0;
                
                // Calculate total
                calculateTotal();
            } else {
                clearFees();
            }
        }
        
        // Clear all fee fields
        function clearFees() {
            document.getElementById('registration_fee').value = '';
            document.getElementById('entrance_fee').value = '';
            document.getElementById('enrollment_fee').value = '';
            document.getElementById('tuition_fee').value = '';
            document.getElementById('caution_money').value = '';
            document.getElementById('hostel_fee_amount').value = '';
            document.getElementById('late_fee').value = '';
            document.getElementById('total_deposit').value = '';
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
    </script>
</x-app-layout>

