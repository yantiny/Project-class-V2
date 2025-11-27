<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // 1. LIHAT SEMUA KUIS DI KURSUS TERTENTU
    public function index(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);

        $quizzes = Quiz::where('course_id', $request->course_id)->get();

        return response()->json(['data' => $quizzes]);
    }

    // 2. BUAT KUIS BARU (Hanya Judul & Deskripsi)
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Cek Kepemilikan Course
        $course = Course::find($request->course_id);
        if ($course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quiz = Quiz::create($request->all());

        return response()->json([
            'message' => 'Quiz created successfully',
            'data' => $quiz
        ], 201);
    }

    // 3. DETAIL KUIS (PENTING: Load Soal & Jawaban)
    public function show($id)
    {
        // Mengambil Quiz + Questions + Answers (Nested Eager Loading)
        $quiz = Quiz::with('questions.answers')->find($id);

        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }

        return response()->json(['data' => $quiz]);
    }

    // 4. HAPUS KUIS
    public function destroy($id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }

        // Cek permission via Course -> User
        if ($quiz->course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted successfully']);
    }
}
