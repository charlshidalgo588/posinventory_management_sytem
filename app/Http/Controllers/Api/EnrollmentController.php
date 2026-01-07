<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the enrollments.
     */
    public function index()
    {
        // Eager load student and course for clarity
        $enrollments = Enrollment::with(['student', 'course'])->get();
        return response()->json($enrollments);
    }

    /**
     * Store a newly created enrollment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id'  => 'required|exists:courses,id',
            'status'     => 'nullable|in:enrolled,dropped,completed',
            'grade'      => 'nullable|string|max:5',
            'enrolled_at'=> 'nullable|date',
        ]);

        $enrollment = Enrollment::create($data);

        return response()->json($enrollment, 201);
    }

    /**
     * Display the specified enrollment.
     */
    public function show(string $id)
    {
        $enrollment = Enrollment::with(['student', 'course'])->findOrFail($id);
        return response()->json($enrollment);
    }

    /**
     * Update the specified enrollment.
     */
    public function update(Request $request, string $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $data = $request->validate([
            'student_id' => 'sometimes|exists:students,id',
            'course_id'  => 'sometimes|exists:courses,id',
            'status'     => 'nullable|in:enrolled,dropped,completed',
            'grade'      => 'nullable|string|max:5',
            'enrolled_at'=> 'nullable|date',
        ]);

        $enrollment->update($data);

        return response()->json(['message' => 'Enrollment updated successfully']);
    }

    /**
     * Remove the specified enrollment.
     */
    public function destroy(string $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json(null, 204);
    }
}
