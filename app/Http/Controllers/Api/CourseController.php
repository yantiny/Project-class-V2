<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // 1. GET ALL COURSES (List semua kursus)
    public function index()
    {
        // Mengambil semua course beserta data instrukturnya (eager loading)
        $courses = Course::with('instructor:id,name')->latest()->get();

        return response()->json([
            'message' => 'List of courses',
            'data' => $courses
        ]);
    }

    // 2. CREATE COURSE (Buat kursus baru)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Magic Eloquent: Otomatis isi user_id dari user yang sedang login
        $course = $request->user()->courses()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course
        ], 201);
    }

    // 3. SHOW DETAIL (Lihat 1 kursus spesifik)
    public function show($id)
    {
        $course = Course::with(['instructor:id,name', 'materials'])->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json([
            'message' => 'Course detail',
            'data' => $course
        ]);
    }

    // 4. UPDATE COURSE (Edit kursus)
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // Security Check: Pastikan yang edit adalah pemilik kursus
        if ($course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $course->update($request->only(['title', 'description']));

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    }

    // 5. DELETE COURSE (Hapus kursus)
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // Security Check: Pastikan yang hapus adalah pemilik kursus
        if ($course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
