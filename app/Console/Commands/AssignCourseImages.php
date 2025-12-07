<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignCourseImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:assign-courses {--force : Force update even if image exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assign images to all courses based on their names and categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting smart image assignment for courses...');
        
        $force = $this->option('force');
        
        $courses = Course::where('status', 'active')
            ->when(!$force, function($query) {
                $query->where(function($q) {
                    $q->whereNull('image')->orWhere('image', '');
                });
            })
            ->with('category')
            ->get();
        
        if ($courses->isEmpty()) {
            $this->info('✅ All courses already have images!');
            return 0;
        }
        
        $this->info("Found {$courses->count()} courses to process...");
        
        $bar = $this->output->createProgressBar($courses->count());
        $bar->start();
        
        $successful = 0;
        $failed = 0;
        
        foreach ($courses as $course) {
            try {
                // Use category name for better color matching, fallback to course name
                $nameForColor = $course->category ? $course->category->name : $course->name;
                $keywords = $this->generateKeywords($course->name);
                
                // Generate image locally
                $imageData = $this->generateImage($course->name, $keywords, $nameForColor);
                
                if ($imageData) {
                    // Generate filename
                    $filename = Str::slug($course->name) . '-' . time() . '.jpg';
                    $filePath = 'courses/' . $filename;
                    
                    // Save to storage
                    Storage::disk('public')->put($filePath, $imageData);
                    
                    // Delete old image if exists
                    if ($course->image) {
                        Storage::disk('public')->delete($course->image);
                    }
                    
                    // Update course
                    $course->image = $filePath;
                    $course->save();
                    
                    $successful++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("\nFailed for {$course->name}: " . $e->getMessage());
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
     * Generate image locally using GD library
     */
    private function generateImage($name, $keywords, $categoryName = '', $width = 800, $height = 600)
    {
        // Color mapping based on category/course type
        $colorMap = [
            'agriculture' => [74, 222, 128],   // green
            'farming' => [74, 222, 128],
            'crop' => [74, 222, 128],
            'business' => [59, 130, 246],      // blue
            'management' => [59, 130, 246],
            'finance' => [59, 130, 246],
            'accounting' => [59, 130, 246],
            'technology' => [139, 92, 246],    // purple
            'computer' => [139, 92, 246],
            'software' => [139, 92, 246],
            'it' => [139, 92, 246],
            'paramedical' => [239, 68, 68],     // red
            'health' => [239, 68, 68],
            'medical' => [239, 68, 68],
            'nursing' => [239, 68, 68],
            'beauty' => [236, 72, 153],        // pink
            'fashion' => [236, 72, 153],
            'music' => [168, 85, 247],         // purple
            'sports' => [34, 197, 94],         // green
            'yoga' => [34, 197, 94],
            'teaching' => [251, 146, 60],      // orange
            'language' => [251, 146, 60],
        ];
        
        $color = [107, 114, 128]; // default gray
        $nameLower = strtolower($categoryName . ' ' . $name . ' ' . $keywords);
        
        foreach ($colorMap as $key => $val) {
            if (stripos($nameLower, $key) !== false) {
                $color = $val;
                break;
            }
        }
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        // Allocate colors
        $bgColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Fill background
        imagefill($image, 0, 0, $bgColor);
        
        // Add text with better formatting
        $text = strtoupper(substr($name, 0, 35));
        $font = 5; // Built-in large font
        
        // Split text into multiple lines if too long
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= 25) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) $lines[] = $currentLine;
                $currentLine = $word;
            }
        }
        if ($currentLine) $lines[] = $currentLine;
        
        // Draw text lines (centered)
        $lineHeight = imagefontheight($font) + 10;
        $totalHeight = count($lines) * $lineHeight;
        $startY = ($height - $totalHeight) / 2;
        
        foreach ($lines as $index => $line) {
            $textWidth = imagefontwidth($font) * strlen($line);
            $x = ($width - $textWidth) / 2;
            $y = $startY + ($index * $lineHeight);
            imagestring($image, $font, $x, $y, $line, $textColor);
        }
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'course_img_') . '.jpg';
        imagejpeg($image, $tempFile, 90);
        imagedestroy($image);
        
        return file_get_contents($tempFile);
    }
}
