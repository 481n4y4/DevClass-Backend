<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Get materials by class.
     */
    public function getByClass($classId)
    {
        $class = Classes::find($classId);
        
        if (!$class) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }
        
        $materials = Material::where('class_id', $classId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $materials
        ]);
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string|max:255',
            'type' => 'nullable|in:file,link,text'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $class = Classes::find($request->class_id);
        
        // Check if user is the teacher of this class
        if ($class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to add materials to this class'
            ], 403);
        }
        
        $material = Material::create([
            'class_id' => $request->class_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $request->file_path,
            'type' => $request->type ?? 'text'
        ]);
        
        return response()->json([
            'status' => 'success',
            'data' => $material,
            'message' => 'Material created successfully'
        ], 201);
    }

    /**
     * Update the specified material.
     */
    public function update(Request $request, $id)
    {
        $material = Material::with('class')->find($id);
        
        if (!$material) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material not found'
            ], 404);
        }
        
        // Check if user is the teacher of the class
        if ($material->class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to update this material'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string|max:255',
            'type' => 'nullable|in:file,link,text'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $material->update($request->only(['title', 'description', 'file_path', 'type']));
        
        return response()->json([
            'status' => 'success',
            'data' => $material,
            'message' => 'Material updated successfully'
        ]);
    }

    /**
     * Remove the specified material.
     */
    public function destroy($id)
    {
        $material = Material::with('class')->find($id);
        
        if (!$material) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material not found'
            ], 404);
        }
        
        // Check if user is the teacher of the class
        if ($material->class->teacher_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this material'
            ], 403);
        }
        
        $material->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Material deleted successfully'
        ]);
    }
}