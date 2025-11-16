<section id="login-section" class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Quick Login
            </h2>
            <p class="text-gray-600 text-sm sm:text-base">
                Choose how you want to sign in – staff panel or student portal.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Staff Login -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                        Staff Login
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Admins and staff can manage admissions, students, and courses from the secure dashboard.
                    </p>
                </div>
                <div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-user-tie mr-2 text-xs"></i>
                        Staff Login
                    </a>
                </div>
            </div>

            <!-- Student Portal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                        Student Portal
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Students can view their details and access portal services using their registered credentials.
                    </p>
                </div>
                <div>
                    <a href="{{ route('student.login') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-user-graduate mr-2 text-xs"></i>
                        Student Portal Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


