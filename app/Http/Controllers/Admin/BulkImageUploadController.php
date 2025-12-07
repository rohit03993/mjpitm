<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkImageUploadController extends Controller
{
    /**
     * Show bulk image upload form
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'image']);
        
        $categories = CourseCategory::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'image']);
        
        return view('admin.bulk-image-upload', compact('courses', 'categories'));
    }

    /**
     * Process bulk image upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:courses,categories'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'mappings' => ['required', 'array'],
        ]);

        $type = $request->input('type');
        $images = $request->file('images');
        $mappings = $request->input('mappings');
        
        $results = [
            'successful' => [],
            'failed' => [],
        ];

        foreach ($images as $index => $image) {
            $mappingKey = $mappings[$index] ?? null;
            
            if (!$mappingKey) {
                $results['failed'][] = [
                    'file' => $image->getClientOriginalName(),
                    'reason' => 'No mapping selected'
                ];
                continue;
            }

            try {
                // Determine storage path based on type
                $storagePath = $type === 'courses' ? 'courses' : 'categories';
                
                // Store the image
                $imagePath = $image->store($storagePath, 'public');
                
                // Update the course or category
                if ($type === 'courses') {
                    $item = Course::find($mappingKey);
                } else {
                    $item = CourseCategory::find($mappingKey);
                }
                
                if (!$item) {
                    $results['failed'][] = [
                        'file' => $image->getClientOriginalName(),
                        'reason' => 'Item not found'
                    ];
                    Storage::disk('public')->delete($imagePath);
                    continue;
                }
                
                // Delete old image if exists
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }
                
                // Update the item with new image path
                $item->image = $imagePath;
                $item->save();
                
                $results['successful'][] = [
                    'file' => $image->getClientOriginalName(),
                    'item' => $item->name,
                    'type' => $type === 'courses' ? 'Course' : 'Category'
                ];
                
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'file' => $image->getClientOriginalName(),
                    'reason' => $e->getMessage()
                ];
            }
        }

        return view('admin.bulk-image-upload-results', compact('results', 'type'));
    }
}

