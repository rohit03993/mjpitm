<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php
                        $user = Auth::user();
                        $dashboardRoute = $user->isSuperAdmin() ? 'superadmin.dashboard' : 'staff.dashboard';
                    @endphp
                    <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs(['dashboard', 'superadmin.dashboard', 'staff.dashboard'])">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notification Bell -->
                <div x-data="{ open: false, notifications: [], count: 0, showPopup: false, popupNotification: null }" 
                     x-init="
                        async function fetchNotifications() {
                            try {
                                const response = await fetch('{{ route('admin.notifications.unread') }}');
                                const data = await response.json();
                                notifications = data.notifications;
                                count = data.count;
                                
                                // Show popup for new notifications
                                if (data.count > 0 && !showPopup) {
                                    showPopup = true;
                                    popupNotification = data.notifications[0];
                                    setTimeout(() => { showPopup = false; }, 5000); // Auto-hide after 5 seconds
                                }
                            } catch (error) {
                                console.error('Error fetching notifications:', error);
                            }
                        }
                        
                        fetchNotifications();
                        setInterval(fetchNotifications, 30000); // Poll every 30 seconds
                     "
                     class="relative">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="count > 0" 
                              x-text="count > 9 ? '9+' : count"
                              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                              style="display: none;"></span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 border border-gray-200"
                         style="display: none;">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            <button @click="
                                fetch('{{ route('admin.notifications.read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                    .then(() => { count = 0; notifications = []; });
                            " class="text-xs text-indigo-600 hover:text-indigo-900">Mark all as read</button>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="p-4 text-center text-sm text-gray-500">No new notifications</div>
                            </template>
                            <template x-for="notification in notifications" :key="notification.id">
                                <a :href="notification.url" 
                                   @click="
                                       fetch(`/admin/notifications/${notification.id}/read`, { 
                                           method: 'POST', 
                                           headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                                       });
                                       count--;
                                       notifications = notifications.filter(n => n.id !== notification.id);
                                   "
                                   class="block p-4 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full"
                                                  :class="notification.registration_type === 'website' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600'">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.student_name"></p>
                                            <p class="text-xs text-gray-500">
                                                <span x-text="notification.registration_type === 'website' ? 'Website Registration' : 'Guest Registration'"></span>
                                                · <span x-text="notification.created_at"></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Popup Notification -->
                    <div x-show="showPopup && popupNotification"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-full"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform translate-x-full"
                         @click="showPopup = false"
                         class="fixed top-4 right-4 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 cursor-pointer"
                         style="display: none;">
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                                          :class="popupNotification?.registration_type === 'website' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600'">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-gray-900" x-text="popupNotification?.student_name"></p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <span x-text="popupNotification?.registration_type === 'website' ? 'New Website Registration' : 'New Guest Registration'"></span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="popupNotification?.created_at"></p>
                                </div>
                                <button @click.stop="showPopup = false" class="ml-2 text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>
                                {{ Auth::user()->name }}
                                @if(Auth::user()->isSuperAdmin())
                                    <span class="ml-1 text-xs text-purple-600 font-semibold">(Admin)</span>
                                @else
                                    <span class="ml-1 text-xs text-blue-600 font-semibold">(Guest)</span>
                                @endif
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        @php
                            $user = Auth::user();
                            $logoutRoute = $user->isSuperAdmin() ? 'superadmin.logout' : 'staff.logout';
                        @endphp
                        <form method="POST" action="{{ route($logoutRoute) }}">
                            @csrf

                            <x-dropdown-link :href="route($logoutRoute)"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php
                $user = Auth::user();
                $dashboardRoute = $user->isSuperAdmin() ? 'superadmin.dashboard' : 'staff.dashboard';
            @endphp
            <x-responsive-nav-link :href="route($dashboardRoute)" :active="request()->routeIs(['dashboard', 'superadmin.dashboard', 'staff.dashboard'])">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                @php
                    $user = Auth::user();
                    $logoutRoute = $user->isSuperAdmin() ? 'superadmin.logout' : 'staff.logout';
                @endphp
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf

                    <x-responsive-nav-link :href="route($logoutRoute)"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
