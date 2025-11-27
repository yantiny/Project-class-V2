<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseProgressController extends Controller
{
    // UPDATE PROGRESS (Misal: User selesai baca PDF / Nonton Video)
    public function update(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'material_id' => 'required|exists:materials,id'
            // Catatan: Di real world, kita mungkin butuh tabel pivot 'material_completions'
            // Tapi untuk simplifikasi sesuai ERD Anda, kita update manual percentage-nya.
        ]);

        // Mari kita asumsikan Frontend mengirim 'percentage' baru,
        // atau kita hitung manual (Completed Materials / Total Materials).
        // Sesuai ERD Anda, kolomnya ada di tabel 'progress'.

        // Cek apakah user sudah punya record progress di course ini?
        $progress = Progress::firstOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $request->course_id],
            ['status' => 'on_progress', 'percentage' => 0]
        );

        // Logic sederhana: Kita terima persentase dari frontend atau hitung kasar
        // Disini saya buat endpoint untuk SET persentase langsung (simplifikasi)
        $newPercentage = $request->input('percentage', $progress->percentage + 10);

        // Cap di 100%
        if ($newPercentage >= 100) {
            $newPercentage = 100;
            $progress->status = 'complete';
        }

        $progress->percentage = $newPercentage;
        $progress->save();

        return response()->json([
            'message' => 'Progress updated',
            'data' => $progress
        ]);
    }

    // LIHAT PROGRESS SAYA
    public function index()
    {
        $progress = Progress::where('user_id', Auth::id())
            ->with('course:id,title,description')
            ->get();

        return response()->json(['data' => $progress]);
    }
}
