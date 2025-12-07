<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseCategory;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AssignCategoryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:assign-categories {--force : Force update even if image exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assign images to all categories based on their names';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting smart image assignment for categories...');
        
        $force = $this->option('force');
        
        $categories = CourseCategory::where('status', 'active')
            ->when(!$force, function($query) {
                $query->where(function($q) {
                    $q->whereNull('image')->orWhere('image', '');
                });
            })
            ->get();
        
        if ($categories->isEmpty()) {
            $this->info('✅ All categories already have images!');
            return 0;
        }
        
        $this->info("Found {$categories->count()} categories to process...");
        
        $bar = $this->output->createProgressBar($categories->count());
        $bar->start();
        
        $successful = 0;
        $failed = 0;
        
        foreach ($categories as $category) {
            try {
                $keywords = $this->generateKeywords($category->name);
                
                // Get actual photo image
                $imageData = $this->getCategoryPhoto($category->name, $keywords);
                
                // If photo fetch failed, create a better-looking gradient image
                if (!$imageData) {
                    $this->warn("\nPhoto fetch failed for {$category->name}, creating enhanced gradient image...");
                    $imageData = $this->createEnhancedGradientImage($category->name, $keywords);
                }
                
                if ($imageData) {
                    // Generate filename
                    $filename = Str::slug($category->name) . '-' . time() . '.jpg';
                    $filePath = 'categories/' . $filename;
                    
                    // Save to storage
                    Storage::disk('public')->put($filePath, $imageData);
                    
                    // Delete old image if exists
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                    
                    // Update category
                    $category->image = $filePath;
                    $category->save();
                    
                    $successful++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("\nFailed for {$category->name}: " . $e->getMessage());
                $failed++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("✅ Successfully assigned: {$successful}");
        $this->error("❌ Failed: {$failed}");
        
        return 0;
    }
    
    /**
     * Generate keywords from name
     */
    private function generateKeywords($name)
    {
        $name = strtolower($name);
        $stopWords = ['course', 'certificate', 'diploma', 'advanced', 'in', 'and', 'the', 'of', 'a', 'an'];
        $words = explode(' ', $name);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        });
        return implode(' ', array_slice($keywords, 0, 3));
    }
    
    /**
     * Get actual photo image based on category keywords
     * Uses multiple strategies to get real photos
     */
    private function getCategoryPhoto($name, $keywords, $width = 800, $height = 600)
    {
        // Map categories to actual photo URLs (Unsplash Source - free, no API key needed)
        $categoryPhotoUrls = [
            'agriculture' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=800&h=600&fit=crop',
            'farming' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=800&h=600&fit=crop',
            'crop' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=800&h=600&fit=crop',
            'business' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
            'management' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
            'finance' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
            'accounting' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
            'technology' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
            'computer' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
            'software' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
            'it' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
            'paramedical' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'health' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'medical' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'nursing' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'allied' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'acupuncture' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'ayurveda' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'unani' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'siddha' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800&h=600&fit=crop',
            'beauty' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&h=600&fit=crop',
            'fashion' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&h=600&fit=crop',
            'music' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=600&fit=crop',
            'sports' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
            'yoga' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=800&h=600&fit=crop',
            'teaching' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop',
            'language' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop',
            'automobile' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=600&fit=crop',
            'electrical' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
            'civil' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=600&fit=crop',
            'hotel' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&h=600&fit=crop',
            'tourism' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&h=600&fit=crop',
        ];
        
        $nameLower = strtolower($name . ' ' . $keywords);
        $photoUrl = null;
        
        // Find matching photo URL
        foreach ($categoryPhotoUrls as $key => $url) {
            if (stripos($nameLower, $key) !== false) {
                $photoUrl = $url;
                break;
            }
        }
        
        // Strategy 1: Use Unsplash Source (actual photos, no API key needed)
        if ($photoUrl) {
            try {
                $response = Http::timeout(20)
                    ->withOptions(['verify' => false])
                    ->get($photoUrl);
                
                if ($response->successful() && $response->body()) {
                    return $response->body();
                }
            } catch (\Exception $e) {
                $this->warn("Unsplash failed, trying Picsum...");
            }
        }
        
        // Strategy 2: Fallback to Picsum Photos with consistent ID
        $consistentId = abs(crc32($name)) % 1000;
        $url = "https://picsum.photos/id/{$consistentId}/{$width}/{$height}";
        
        try {
            $response = Http::timeout(20)
                ->withOptions(['verify' => false])
                ->get($url);
            
            if ($response->successful() && $response->body()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            $this->warn("Picsum failed: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Create enhanced gradient image as fallback (looks more like a photo)
     */
    private function createEnhancedGradientImage($name, $keywords, $width = 800, $height = 600)
    {
        // Category-based color gradients (more photo-like)
        $gradientColors = [
            'agriculture' => [[34, 197, 94], [22, 163, 74]],   // Green gradient
            'farming' => [[34, 197, 94], [22, 163, 74]],
            'crop' => [[34, 197, 94], [22, 163, 74]],
            'business' => [[59, 130, 246], [37, 99, 235]],     // Blue gradient
            'management' => [[59, 130, 246], [37, 99, 235]],
            'finance' => [[59, 130, 246], [37, 99, 235]],
            'accounting' => [[59, 130, 246], [37, 99, 235]],
            'technology' => [[139, 92, 246], [124, 58, 237]],  // Purple gradient
            'computer' => [[139, 92, 246], [124, 58, 237]],
            'software' => [[139, 92, 246], [124, 58, 237]],
            'it' => [[139, 92, 246], [124, 58, 237]],
            'paramedical' => [[239, 68, 68], [220, 38, 38]],   // Red gradient
            'health' => [[239, 68, 68], [220, 38, 38]],
            'medical' => [[239, 68, 68], [220, 38, 38]],
            'nursing' => [[239, 68, 68], [220, 38, 38]],
            'allied' => [[239, 68, 68], [220, 38, 38]],
            'beauty' => [[236, 72, 153], [219, 39, 119]],       // Pink gradient
            'fashion' => [[236, 72, 153], [219, 39, 119]],
            'music' => [[168, 85, 247], [147, 51, 234]],       // Purple gradient
            'sports' => [[34, 197, 94], [22, 163, 74]],        // Green gradient
            'yoga' => [[34, 197, 94], [22, 163, 74]],
            'teaching' => [[251, 146, 60], [249, 115, 22]],    // Orange gradient
            'language' => [[251, 146, 60], [249, 115, 22]],
        ];
        
        $nameLower = strtolower($name . ' ' . $keywords);
        $colors = [[107, 114, 128], [75, 85, 99]]; // Default gray gradient
        
        foreach ($gradientColors as $key => $val) {
            if (stripos($nameLower, $key) !== false) {
                $colors = $val;
                break;
            }
        }
        
        // Create image with gradient
        $image = imagecreatetruecolor($width, $height);
        
        // Create gradient effect
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = (int)($colors[0][0] + ($colors[1][0] - $colors[0][0]) * $ratio);
            $g = (int)($colors[0][1] + ($colors[1][1] - $colors[0][1]) * $ratio);
            $b = (int)($colors[0][2] + ($colors[1][2] - $colors[0][2]) * $ratio);
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
        
        // Add subtle pattern overlay for depth
        for ($i = 0; $i < 50; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $size = rand(20, 100);
            $alpha = imagecolorallocatealpha($image, 255, 255, 255, 90);
            imagefilledellipse($image, $x, $y, $size, $size, $alpha);
        }
        
        // Add category name text
        $text = strtoupper(substr($name, 0, 30));
        $font = 5;
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = ($width - $textWidth) / 2;
        $y = ($height - imagefontheight($font)) / 2;
        imagestring($image, $font, $x, $y, $text, $textColor);
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'category_img_') . '.jpg';
        imagejpeg($image, $tempFile, 90);
        imagedestroy($image);
        
        return file_get_contents($tempFile);
    }
}
