<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizResultController extends Controller
{
    // SISWA MENGIRIM JAWABAN (SUBMIT EXAM)
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        $quiz = Quiz::with('questions')->find($request->quiz_id);
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        // Loop setiap jawaban siswa
        foreach ($request->answers as $userAnswer) {
            // Cek di database apakah jawaban ini is_correct = true
            $answer = Answer::find($userAnswer['answer_id']);

            // Validasi: Pastikan jawaban itu milik pertanyaan yang benar
            if ($answer && $answer->question_id == $userAnswer['question_id'] && $answer->is_correct) {
                $correctAnswers++;
            }
        }

        // Rumus Nilai: (Benar / Total Soal) * 100
        $score = ($totalQuestions > 0) ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Simpan Hasil ke Database
        $result = QuizResult::create([
            'user_id' => Auth::id(),
            'quiz_id' => $request->quiz_id,
            'score' => $score,
            'completed_at' => now(), // Laravel 11 otomatis handle timestamp, tapi kita eksplisitkan
        ]);

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'data' => $result
        ], 201);
    }

    // LIHAT RIWAYAT NILAI SISWA
    public function index(Request $request)
    {
        // Menampilkan hasil quiz milik user yang sedang login saja
        $results = QuizResult::where('user_id', Auth::id())
            ->with('quiz:id,title')
            ->latest()
            ->get();

        return response()->json(['data' => $results]);
    }
}
