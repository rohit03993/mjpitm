<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Institute') }}
            </h2>
            <a href="{{ route('superadmin.institutes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Institutes
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

            <form method="POST" action="{{ route('superadmin.institutes.update', $institute->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Institute Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Institute Name *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $institute->name)" required placeholder="e.g., Mahatma Jyotiba Phule Institute of Technology & Management" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Institute Code -->
                        <div>
                            <x-input-label for="institute_code" :value="__('Institute Code')" />
                            <x-text-input id="institute_code" class="block mt-1 w-full uppercase" type="text" name="institute_code" :value="old('institute_code', $institute->institute_code)" placeholder="e.g., MJPITM" maxlength="10" />
                            <x-input-error :messages="$errors->get('institute_code')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Code used in roll number generation (e.g., MJPITM, MJPIPS). Uppercase letters and numbers only.</p>
                        </div>

                        <!-- Domain -->
                        <div>
                            <x-input-label for="domain" :value="__('Domain *')" />
                            <x-text-input id="domain" class="block mt-1 w-full" type="text" name="domain" :value="old('domain', $institute->domain)" required placeholder="e.g., mjpitm.in" />
                            <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">This domain will be used for routing and identification.</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status *')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="active" {{ old('status', $institute->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $institute->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Brief description of the institute...">{{ old('description', $institute->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Contact Us (Footer)</h3>
                        <p class="text-sm text-blue-700 mt-1">These appear in the site footer. Leave blank to hide.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="contact_address" :value="__('Location / Address')" />
                            <x-text-input id="contact_address" class="block mt-1 w-full" type="text" name="contact_address" :value="old('contact_address', $institute->contact_address)" placeholder="e.g., Agra, Uttar Pradesh, India" />
                            <x-input-error :messages="$errors->get('contact_address')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="contact_email" :value="__('Email')" />
                            <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email', $institute->contact_email)" placeholder="e.g., info@mjpitm.in" />
                            <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="contact_phone" :value="__('Phone')" />
                            <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone', $institute->contact_phone)" placeholder="e.g., +91-XXXXX-XXXXX" />
                            <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Marksheet Template (Issued Marksheet PDF)</h3>
                        <p class="text-sm text-blue-700 mt-1">Uploads are per institute (MJPITM/MJPIPS). Images are auto-resized and compressed when possible.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="marksheet_header_logo" :value="__('Header Logo (optional)')" />
                            <input id="marksheet_header_logo" type="file" name="marksheet_header_logo" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_header_logo')" class="mt-2" />
                            @if($institute->marksheet_header_logo)
                                <div class="mt-2">
                                    <div class="text-xs text-gray-500 mb-1">Current:</div>
                                    <img src="{{ asset('storage/' . $institute->marksheet_header_logo) }}" alt="Header logo" class="h-16 border border-gray-200 bg-white rounded" />
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="marksheet_watermark_image" :value="__('Watermark Image (optional)')" />
                            <input id="marksheet_watermark_image" type="file" name="marksheet_watermark_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_watermark_image')" class="mt-2" />
                            @if($institute->marksheet_watermark_image)
                                <div class="mt-2">
                                    <div class="text-xs text-gray-500 mb-1">Current:</div>
                                    <img src="{{ asset('storage/' . $institute->marksheet_watermark_image) }}" alt="Watermark" class="h-20 border border-gray-200 bg-white rounded" />
                                </div>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">Used as a faint watermark behind the marksheet content.</p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-600 mb-2">Footer row (left → right, e.g. Skill India, ISO, Beti Bachao, Swachh Bharat). Upload PNG/JPG with transparent background if possible.</p>
                        </div>
                        <div>
                            <x-input-label for="marksheet_footer_logo_1" :value="__('Footer Logo 1 (left, optional)')" />
                            <input id="marksheet_footer_logo_1" type="file" name="marksheet_footer_logo_1" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_footer_logo_1')" class="mt-2" />
                            @if($institute->marksheet_footer_logo_1)
                                <img src="{{ asset('storage/' . $institute->marksheet_footer_logo_1) }}" alt="Footer logo 1" class="mt-2 h-12 border border-gray-200 bg-white rounded shadow-sm" />
                            @endif
                        </div>
                        <div>
                            <x-input-label for="marksheet_footer_logo_2" :value="__('Footer Logo 2 (optional)')" />
                            <input id="marksheet_footer_logo_2" type="file" name="marksheet_footer_logo_2" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_footer_logo_2')" class="mt-2" />
                            @if($institute->marksheet_footer_logo_2)
                                <img src="{{ asset('storage/' . $institute->marksheet_footer_logo_2) }}" alt="Footer logo 2" class="mt-2 h-12 border border-gray-200 bg-white rounded shadow-sm" />
                            @endif
                        </div>
                        <div>
                            <x-input-label for="marksheet_footer_logo_3" :value="__('Footer Logo 3 (optional)')" />
                            <input id="marksheet_footer_logo_3" type="file" name="marksheet_footer_logo_3" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_footer_logo_3')" class="mt-2" />
                            @if($institute->marksheet_footer_logo_3)
                                <img src="{{ asset('storage/' . $institute->marksheet_footer_logo_3) }}" alt="Footer logo 3" class="mt-2 h-12 border border-gray-200 bg-white rounded shadow-sm" />
                            @endif
                        </div>
                        <div>
                            <x-input-label for="marksheet_footer_logo_4" :value="__('Footer Logo 4 (right, optional)')" />
                            <input id="marksheet_footer_logo_4" type="file" name="marksheet_footer_logo_4" accept="image/*" class="block mt-1 w-full text-sm text-gray-700" />
                            <x-input-error :messages="$errors->get('marksheet_footer_logo_4')" class="mt-2" />
                            @if($institute->marksheet_footer_logo_4)
                                <img src="{{ asset('storage/' . $institute->marksheet_footer_logo_4) }}" alt="Footer logo 4" class="mt-2 h-12 border border-gray-200 bg-white rounded shadow-sm" />
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('superadmin.institutes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Update Institute') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

