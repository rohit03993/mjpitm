<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('superadmin.users.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Full Name *')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="email" :value="__('Email *')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role *')" />
                                <select id="role" name="role" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Role</option>
                                    <option value="institute_admin" {{ old('role', $preselectedRole ?? '') === 'institute_admin' ? 'selected' : '' }}>Institute Admin (Guest)</option>
                                    <option value="staff" {{ old('role', $preselectedRole ?? '') === 'staff' ? 'selected' : '' }}>Staff (Helper)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    <strong>Institute Admin:</strong> Uses Guest Login, manages own institute<br>
                                    <strong>Staff:</strong> Uses Admin Login, helps Super Admin with tasks<br>
                                    <em>Note: Super Admin role cannot be created through this interface.</em>
                                </p>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="institute_id" :value="__('Primary Institute (optional)')" />
                                <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Institutes</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{ $institute->id }}" {{ (string)old('institute_id') === (string)$institute->id ? 'selected' : '' }}>
                                            {{ $institute->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">This is mainly for reference; admins can register students for both institutes.</p>
                                <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status *')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password *')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password *')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <a href="{{ route('superadmin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Back to Admins
                            </a>
                            <x-primary-button>
                                {{ __('Create Admin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


