<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\CourseCategory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Footer "Programs": top 5 categories for tech and paramedical layouts
        View::composer(['layouts.tech', 'layouts.paramedical'], function ($view) {
            $instituteId = session('current_institute_id');
            $footerCategories = collect();
            if ($instituteId) {
                $footerCategories = CourseCategory::where('institute_id', $instituteId)
                    ->where('status', 'active')
                    ->withCount(['activeCourses' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get()
                    ->where('active_courses_count', '>', 0)
                    ->take(5)
                    ->values();
            }
            $view->with('footerCategories', $footerCategories);
        });
    }
}
