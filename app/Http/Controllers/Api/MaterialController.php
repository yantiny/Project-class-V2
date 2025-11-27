<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // 1. CREATE MATERIAL (Upload File)
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:pdf,photo,word,video,ppt,xlsx',
            // Validasi file max 10MB (10240 KB)
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp4,jpg,jpeg,png|max:10240',
        ]);

        // Cek apakah User adalah pemilik Course ini?
        $course = Course::find($request->course_id);
        if ($course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized. You do not own this course.'], 403);
        }

        // Logic Upload File
        if ($request->hasFile('file')) {
            // Simpan ke folder 'materials' di storage/app/public
            $path = $request->file('file')->store('materials', 'public');

            // Generate URL lengkap agar bisa diakses frontend
            $url = url('storage/' . $path);
        }

        // Simpan ke Database
        $material = Material::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content_type' => $request->content_type,
            'content_url' => $url, // URL file yang bisa di-klik
        ]);

        return response()->json([
            'message' => 'Material uploaded successfully',
            'data' => $material
        ], 201);
    }

    // 2. GET MATERIALS BY COURSE (Lihat materi di kursus tertentu)
    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $materials = Material::where('course_id', $request->course_id)->get();

        return response()->json([
            'message' => 'List of materials',
            'data' => $materials
        ]);
    }

    // 3. DELETE MATERIAL (Hapus file dan record)
    public function destroy($id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        // Cek Kepemilikan via Course
        if ($material->course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus file fisik di storage
        // Ambil relative path dari URL (karena di DB tersimpan URL lengkap)
        $relativePath = str_replace(url('storage/'), '', $material->content_url);
        Storage::disk('public')->delete($relativePath);

        // Hapus record di DB
        $material->delete();

        return response()->json(['message' => 'Material deleted successfully']);
    }
}
