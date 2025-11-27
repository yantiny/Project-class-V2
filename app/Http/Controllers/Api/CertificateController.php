<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Progress;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    // 1. KLAIM SERTIFIKAT (Generate Baru)
    public function store(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);

        $user = Auth::user();
        $courseId = $request->course_id;

        // CEK 1: Apakah sudah pernah klaim? Jangan double.
        $existingCert = Certificate::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();
        if ($existingCert) {
            return response()->json([
                'message' => 'Certificate already issued',
                'data' => $existingCert
            ], 200); // 200 OK karena data sudah ada
        }

        // CEK 2: Syarat Kelulusan (Pilih salah satu logika di bawah)

        // Opsi A: Cek Progress harus 100% (Complete)
        $progress = Progress::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'complete')
            ->first();

        // Opsi B: Atau Cek Nilai Kuis terakhir harus lulus (misal > 70)
        // $passedQuiz = QuizResult::where('user_id', $user->id)...

        if (!$progress) {
            return response()->json([
                'message' => 'Cannot claim certificate. Please complete the course first.'
            ], 403);
        }

        // GENERATE SERTIFIKAT
        // Kita buat kode unik: CERT-{Tahun}-{RandomString}
        $code = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'certificate_code' => $code,
            'certificate_url' => url("/certificates/view/{$code}"), // URL dummy untuk view
            'issued_at' => now(),
        ]);

        return response()->json([
            'message' => 'Certificate issued successfully!',
            'data' => $certificate
        ], 201);
    }

    // 2. LIHAT SERTIFIKAT SAYA
    public function index()
    {
        $certificates = Certificate::with('course:id,title')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['data' => $certificates]);
    }
}
