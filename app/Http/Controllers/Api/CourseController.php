<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        return response()->json(Course::all());
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::create($data);

        return response()->json($course, 201);
    }

    /**
     * Display the specified course.
     */
    public function show(string $id)
    {
        return response()->json(Course::findOrFail($id));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($data);

        return response()->json(['message' => 'Course updated successfully']);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(null, 204);
    }
}
