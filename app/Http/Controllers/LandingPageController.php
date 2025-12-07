<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institute;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    /**
     * Display the landing page based on domain
     */
    public function index(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        // If no institute detected (admin access or localhost), show default
        if (!$institute) {
            return view('welcome');
        }

        // Map institute to view
        $instituteViews = [
            1 => 'institutes.tech.home',        // Tech Institute (mjpitm.in)
            2 => 'institutes.paramedical.home', // Paramedical Institute (mjpips.in)
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
        ]);
    }

    /**
     * Display about page for the institute
     */
    public function about(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        if (!$institute) {
            return redirect('/');
        }

        $instituteViews = [
            1 => 'institutes.tech.about',
            2 => 'institutes.paramedical.about',
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
        ]);
    }

    /**
     * Display courses page - shows list of categories
     */
    public function courses(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        if (!$institute) {
            return redirect('/');
        }

        // Get all active categories with course count
        $categories = \App\Models\CourseCategory::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->withCount(['activeCourses' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $instituteViews = [
            1 => 'institutes.tech.courses',
            2 => 'institutes.paramedical.courses',
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
            'categories' => $categories,
        ]);
    }

    /**
     * Display courses in a specific category
     */
    public function categoryCourses(Request $request, $category)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        if (!$institute) {
            return redirect('/');
        }

        // Find category by slug or name
        // First, try to match by slug (exact match after converting name to slug)
        $categoryModel = \App\Models\CourseCategory::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->get()
            ->first(function($cat) use ($category) {
                // Try exact slug match
                $categorySlug = Str::slug($cat->name);
                if ($categorySlug === $category) {
                    return true;
                }
                
                // Try case-insensitive name match
                $categoryNameLower = strtolower(str_replace(['/', '-'], ' ', $cat->name));
                $searchName = strtolower(str_replace('-', ' ', $category));
                if (strpos($categoryNameLower, $searchName) !== false || strpos($searchName, $categoryNameLower) !== false) {
                    return true;
                }
                
                // Try code match
                if ($cat->code && strtolower($cat->code) === strtolower($category)) {
                    return true;
                }
                
                return false;
            });
        
        if (!$categoryModel) {
            abort(404, 'Category not found');
        }

        // Get active courses in this category
        $courses = \App\Models\Course::where('institute_id', $instituteId)
            ->where('category_id', $categoryModel->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $instituteViews = [
            1 => 'institutes.tech.category-courses',
            2 => 'institutes.paramedical.category-courses',
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
            'category' => $categoryModel,
            'courses' => $courses,
        ]);
    }

    /**
     * Get smart category description based on category name
     */
    public static function getCategoryDescription($categoryName)
    {
        $name = strtolower($categoryName);
        
        $descriptions = [
            'acupuncture' => 'Acupuncture is an ancient healing practice that involves inserting thin needles into specific points on the body to promote natural healing and improve functioning. Our comprehensive acupuncture courses provide in-depth training in traditional and modern techniques.',
            'agriculture' => 'Agriculture courses focus on modern farming techniques, sustainable practices, and agricultural management. Learn about crop production, soil management, and agricultural technology to excel in the farming industry.',
            'allied health' => 'Allied health courses prepare students for essential healthcare support roles. These programs cover various medical support services that complement the work of physicians and nurses.',
            'automobiles' => 'Automobile education courses provide comprehensive training in vehicle mechanics, maintenance, and repair. Learn about modern automotive technology, engine systems, and diagnostic techniques.',
            'automobiles education' => 'Automobile education courses provide comprehensive training in vehicle mechanics, maintenance, and repair. Learn about modern automotive technology, engine systems, and diagnostic techniques.',
            'ayurveda' => 'Ayurveda is a traditional Indian system of medicine that emphasizes holistic wellness. Our Ayurveda courses teach ancient healing principles, herbal medicine, and natural treatment methods.',
            'beauty' => 'Beauty courses cover cosmetology, skincare, makeup artistry, and salon management. Develop professional skills in beauty therapy and aesthetics to build a successful career in the beauty industry.',
            'business' => 'Business courses provide essential knowledge in management, entrepreneurship, finance, and marketing. Develop the skills needed to succeed in today\'s competitive business environment.',
            'paramedical' => 'Paramedical courses train students in healthcare support services including medical laboratory technology, radiology, and patient care. These programs prepare you for vital roles in the healthcare sector.',
            'technical' => 'Technical courses focus on practical skills in engineering, technology, and applied sciences. Gain hands-on experience and industry-relevant knowledge to excel in technical careers.',
            'management' => 'Management courses develop leadership skills, strategic thinking, and organizational expertise. Learn to lead teams, manage projects, and drive business success.',
            'health' => 'Health courses cover various aspects of healthcare, wellness, and medical support services. Prepare for rewarding careers in the healthcare industry.',
            'education' => 'Education courses prepare you for teaching and educational administration roles. Learn effective teaching methods, curriculum development, and educational leadership.',
        ];

        // Try exact match first
        if (isset($descriptions[$name])) {
            return $descriptions[$name];
        }

        // Try partial matches
        foreach ($descriptions as $key => $description) {
            if (strpos($name, $key) !== false || strpos($key, $name) !== false) {
                return $description;
            }
        }

        // Default description
        return 'Explore our comprehensive range of courses in ' . $categoryName . '. Our programs are designed to provide practical skills and knowledge for your career success.';
    }
}
