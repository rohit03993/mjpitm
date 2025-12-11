<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('superadmin.users.update', $admin) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Full Name *')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $admin->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="email" :value="__('Email *')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $admin->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role *')" />
                                <select id="role" name="role" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required {{ $admin->isSuperAdmin() ? 'disabled' : '' }}>
                                    <option value="institute_admin" {{ old('role', $admin->role) === 'institute_admin' ? 'selected' : '' }}>Institute Admin (Guest)</option>
                                    <option value="staff" {{ old('role', $admin->role) === 'staff' ? 'selected' : '' }}>Staff (Helper)</option>
                                    @if($admin->isSuperAdmin())
                                        <option value="super_admin" selected>Super Admin (Cannot be changed)</option>
                                    @endif
                                </select>
                                @if($admin->isSuperAdmin())
                                    <input type="hidden" name="role" value="super_admin">
                                @endif
                                <p class="mt-1 text-xs text-gray-500">
                                    <strong>Institute Admin:</strong> Uses Guest Login, manages own institute<br>
                                    <strong>Staff:</strong> Uses Admin Login, helps Super Admin with tasks<br>
                                    @if($admin->isSuperAdmin())
                                        <em>Note: Super Admin role cannot be changed.</em>
                                    @else
                                        <em>Note: Super Admin role cannot be assigned through this interface.</em>
                                    @endif
                                </p>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="institute_id" :value="__('Primary Institute (optional)')" />
                                <select id="institute_id" name="institute_id" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Institutes</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{ $institute->id }}" {{ (string)old('institute_id', $admin->institute_id) === (string)$institute->id ? 'selected' : '' }}>
                                            {{ $institute->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Admins can still register students for both institutes.</p>
                                <x-input-error :messages="$errors->get('institute_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status *')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('New Password (optional)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password.</p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <a href="{{ route('superadmin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Back to Admins
                            </a>
                            <x-primary-button>
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


