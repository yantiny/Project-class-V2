<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    // BUAT SOAL SEKALIGUS JAWABANNYA
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2', // Minimal 2 pilihan jawaban
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        $quiz = Quiz::find($request->quiz_id);

        // Cek Permission
        if ($quiz->course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Gunakan Transaction agar jika gagal simpan jawaban, soal tidak terbuat
        return DB::transaction(function () use ($request) {
            // 1. Simpan Soal
            $question = Question::create([
                'quiz_id' => $request->quiz_id,
                'question_text' => $request->question_text,
            ]);

            // 2. Simpan Jawaban (Looping array)
            foreach ($request->answers as $answer) {
                $question->answers()->create([
                    'answer_text' => $answer['answer_text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            return response()->json([
                'message' => 'Question and answers added successfully',
                'data' => $question->load('answers')
            ], 201);
        });
    }

    // HAPUS SOAL
    public function destroy($id)
    {
        $question = Question::find($id);
        if (!$question) return response()->json(['message' => 'Not found'], 404);

        if ($question->quiz->course->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete(); // Jawaban otomatis terhapus karena cascade delete di migration
        return response()->json(['message' => 'Question deleted']);
    }
}
