<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    private function storeTemplateImage(Request $request, Institute $institute, string $field, int $maxWidth): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        if (!$file || !$file->isValid()) {
            return null;
        }

        $dir = 'marksheet/templates/' . $institute->id;
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $ext = 'jpg';
        }

        $baseName = Str::slug($field) . '-' . time();
        $path = $dir . '/' . $baseName . '.' . $ext;

        // Try to auto-resize/compress with GD; fallback to plain store.
        try {
            if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                $data = file_get_contents($file->getRealPath());
                $img = $data ? @imagecreatefromstring($data) : false;
                if ($img !== false) {
                    $w = imagesx($img);
                    $h = imagesy($img);
                    $scale = ($w > 0) ? min(1, $maxWidth / $w) : 1;
                    $newW = (int) max(1, floor($w * $scale));
                    $newH = (int) max(1, floor($h * $scale));

                    $dst = imagecreatetruecolor($newW, $newH);
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

                    imagecopyresampled($dst, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

                    ob_start();
                    if ($ext === 'png' && function_exists('imagepng')) {
                        imagepng($dst, null, 6);
                    } elseif ($ext === 'webp' && function_exists('imagewebp')) {
                        imagewebp($dst, null, 82);
                    } else {
                        imagejpeg($dst, null, 82);
                        $path = $dir . '/' . $baseName . '.jpg';
                    }
                    $out = ob_get_clean();

                    imagedestroy($img);
                    imagedestroy($dst);

                    if ($out) {
                        Storage::disk('public')->put($path, $out);
                        return $path;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        return $file->storeAs($dir, basename($path), 'public');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $institutes = Institute::withCount(['students', 'courses', 'admins'])
            ->latest()
            ->paginate(resolve_per_page($request->query('per_page')))
            ->withQueryString();

        return view('superadmin.institutes.index', compact('institutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.institutes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institute_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'domain' => ['required', 'string', 'max:255', 'unique:institutes,domain'],
            'description' => ['nullable', 'string'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Institute::create($validated);

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institute $institute)
    {
        $institute->loadCount(['students', 'courses', 'admins']);
        $institute->load(['courses', 'admins']);

        return view('superadmin.institutes.show', compact('institute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institute $institute)
    {
        return view('superadmin.institutes.edit', compact('institute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institute $institute)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institute_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'domain' => ['required', 'string', 'max:255', Rule::unique('institutes', 'domain')->ignore($institute->id)],
            'description' => ['nullable', 'string'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'marksheet_header_logo' => ['nullable', 'image', 'max:4096'],
            'marksheet_watermark_image' => ['nullable', 'image', 'max:6144'],
            'marksheet_footer_logo_1' => ['nullable', 'image', 'max:2048'],
            'marksheet_footer_logo_2' => ['nullable', 'image', 'max:2048'],
            'marksheet_footer_logo_3' => ['nullable', 'image', 'max:2048'],
            'marksheet_footer_logo_4' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $institute->update(collect($validated)->except([
            'marksheet_header_logo',
            'marksheet_watermark_image',
            'marksheet_footer_logo_1',
            'marksheet_footer_logo_2',
            'marksheet_footer_logo_3',
        ])->toArray());

        $map = [
            'marksheet_header_logo' => 700,
            'marksheet_watermark_image' => 1200,
            'marksheet_footer_logo_1' => 220,
            'marksheet_footer_logo_2' => 220,
            'marksheet_footer_logo_3' => 220,
            'marksheet_footer_logo_4' => 220,
        ];
        foreach ($map as $field => $maxWidth) {
            if ($request->hasFile($field)) {
                $newPath = $this->storeTemplateImage($request, $institute, $field, $maxWidth);
                if ($newPath) {
                    $oldPath = $institute->getAttribute($field);
                    $institute->setAttribute($field, $newPath);
                    $institute->save();
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
        }

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institute $institute)
    {
        // Check if institute has students or courses
        if ($institute->students()->count() > 0 || $institute->courses()->count() > 0) {
            return redirect()->route('superadmin.institutes.index')
                ->with('error', 'Cannot delete institute. There are students or courses associated with this institute.');
        }

        $institute->delete();

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute deleted successfully.');
    }
}

