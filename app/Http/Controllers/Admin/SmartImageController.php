<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SmartImageController extends Controller
{
    /**
     * Show smart image assignment page
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->whereNull('image')
            ->orWhere('image', '')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'category_id']);
        
        $categories = CourseCategory::where('status', 'active')
            ->whereNull('image')
            ->orWhere('image', '')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
        
        return view('admin.smart-image-assignment', compact('courses', 'categories'));
    }

    /**
     * Generate keywords from course/category name
     */
    private function generateKeywords($name)
    {
        // Convert to lowercase and extract meaningful words
        $name = strtolower($name);
        
        // Remove common words
        $stopWords = ['course', 'certificate', 'diploma', 'advanced', 'in', 'and', 'the', 'of', 'a', 'an'];
        $words = explode(' ', $name);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        });
        
        // Take first 2-3 meaningful keywords
        $keywords = array_slice($keywords, 0, 3);
        
        return implode(' ', $keywords);
    }

    /**
     * Fetch image using multiple strategies
     */
    private function fetchImage($keywords, $name, $width = 800, $height = 600)
    {
        // Strategy 1: Try Picsum Photos with keyword-based seed
        try {
            $seed = crc32($keywords); // Generate consistent seed from keywords
            $url = "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
            $response = Http::timeout(5)->get($url);
            
            if ($response->successful() && $response->header('Content-Type') && strpos($response->header('Content-Type'), 'image') !== false) {
                return $response->body();
            }
        } catch (\Exception $e) {
            \Log::debug("Picsum failed, trying next method", ['keywords' => $keywords]);
        }
        
        // Strategy 2: Use placeholder service with smart colors and text
        return $this->getSmartPlaceholder($name, $keywords, $width, $height);
    }

    /**
     * Get smart placeholder image with category-based colors
     */
    private function getSmartPlaceholder($name, $keywords, $width = 800, $height = 600)
    {
        // Determine color based on keywords/category
        $colorMap = [
            'agriculture' => '4ade80', // green
            'farming' => '4ade80',
            'crop' => '4ade80',
            'business' => '3b82f6', // blue
            'management' => '3b82f6',
            'finance' => '3b82f6',
            'accounting' => '3b82f6',
            'technology' => '8b5cf6', // purple
            'computer' => '8b5cf6',
            'software' => '8b5cf6',
            'paramedical' => 'ef4444', // red
            'health' => 'ef4444',
            'medical' => 'ef4444',
            'nursing' => 'ef4444',
            'diploma' => 'f59e0b', // amber
            'certificate' => 'f59e0b',
        ];
        
        $color = '6b7280'; // default gray
        $nameLower = strtolower($name . ' ' . $keywords);
        
        foreach ($colorMap as $key => $val) {
            if (stripos($nameLower, $key) !== false) {
                $color = $val;
                break;
            }
        }
        
        // Create a nice placeholder with gradient and text
        $text = urlencode(substr($name, 0, 30));
        $bgColor = $color;
        $textColor = 'ffffff';
        
        // Use placeholder.com with better formatting
        return "https://via.placeholder.com/{$width}x{$height}/{$bgColor}/{$textColor}?text=" . $text;
    }

    /**
     * Process smart image assignment
     */
    public function assign(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:courses,categories'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'exists:' . ($request->input('type') === 'courses' ? 'courses' : 'course_categories') . ',id'],
        ]);

        $type = $request->input('type');
        $itemIds = $request->input('items');
        
        $results = [
            'successful' => [],
            'failed' => [],
        ];

        foreach ($itemIds as $itemId) {
            try {
                if ($type === 'courses') {
                    $item = Course::find($itemId);
                } else {
                    $item = CourseCategory::find($itemId);
                }
                
                if (!$item) {
                    $results['failed'][] = [
                        'item' => "ID: {$itemId}",
                        'reason' => 'Item not found'
                    ];
                    continue;
                }
                
                // Generate keywords from item name
                $keywords = $this->generateKeywords($item->name);
                
                // Fetch image using smart strategies
                $imageUrl = $this->fetchImage($keywords, $item->name);
                $imageData = Http::timeout(10)->get($imageUrl)->body();
                
                if ($imageData) {
                    // Determine storage path
                    $storagePath = $type === 'courses' ? 'courses' : 'categories';
                    
                    // Generate unique filename
                    $filename = Str::slug($item->name) . '-' . time() . '.jpg';
                    $filePath = $storagePath . '/' . $filename;
                    
                    // Save image to storage
                    Storage::disk('public')->put($filePath, $imageData);
                    
                    // Delete old image if exists
                    if ($item->image) {
                        Storage::disk('public')->delete($item->image);
                    }
                    
                    // Update item
                    $item->image = $filePath;
                    $item->save();
                    
                    $results['successful'][] = [
                        'item' => $item->name,
                        'keywords' => $keywords,
                        'type' => $type === 'courses' ? 'Course' : 'Category'
                    ];
                } else {
                    $results['failed'][] = [
                        'item' => $item->name,
                        'reason' => 'Could not fetch image'
                    ];
                }
                
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'item' => $item->name ?? "ID: {$itemId}",
                    'reason' => $e->getMessage()
                ];
            }
        }

        return view('admin.smart-image-results', compact('results', 'type'));
    }

    /**
     * Assign images to all items without images
     */
    public function assignAll(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:courses,categories'],
        ]);

        $type = $request->input('type');
        
        if ($type === 'courses') {
            $items = Course::where('status', 'active')
                ->where(function($query) {
                    $query->whereNull('image')->orWhere('image', '');
                })
                ->get();
        } else {
            $items = CourseCategory::where('status', 'active')
                ->where(function($query) {
                    $query->whereNull('image')->orWhere('image', '');
                })
                ->get();
        }

        $itemIds = $items->pluck('id')->toArray();
        
        // Create a new request with the item IDs
        $newRequest = new Request([
            'type' => $type,
            'items' => $itemIds
        ]);
        
        return $this->assign($newRequest);
    }
}

